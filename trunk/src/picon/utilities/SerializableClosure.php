<?php

/**
 * Picon Framework
 * http://code.google.com/p/picon-framework/
 *
 * Copyright (C) 2011-2012 Martin Cassidy <martin.cassidy@webquub.com>

 * Picon Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Picon Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Picon Framework.  If not, see <http://www.gnu.org/licenses/>.
 * */
namespace picon;

/**
 * A wrapper for closures enabling them to be serialized.
 * IMPORTANT NOTE: This is highly experimental and needs further improvment and testing
 * 
 * A closure is serialized by deconstructing it into its parts, namely the string
 * that represents its code and the paramaters it uses. The code is extracted
 * with reflection and SplFileObject, the paramaters are extracted with reflection.
 * 
 * The code string and paramters are all that is serailized. On unserialize the code
 * and paramters are used to reconstruct the closure using eval().
 * 
 * The usage of eval mean that SplFileObject can no longer be used on a reconstructed closure.
 * However, this is solved by storing the previously extracted code for future use.
 * 
 * Any closure which is defined within a reconstructed closure can also not have SplFileObject
 * used on it, as it was also defined by eval()'d code. To prevent this being a problem, all extracted
 * code is processed so that an closures declared within have their own code isolated so that it may be
 * used later when it is needed and would otherwise be un-obtainable with SplFileObject.
 * 
 * @todo test in PHP 5.4.x
 * @author Martin Cassidy
 * @package utilities
 */
class SerializableClosure
{
    private $closure;
    private $code;
    private $arguments;
    private $source;
    private $reflection;
    private $imports;
    private $declaredNameSpace;
    
    public function __construct($closure, $code = null)
    {
        $this->validateClosure($closure);
        $this->reflection = new \ReflectionFunction($closure);
        
        if($code==null)
        {
            $this->evaluate($this->reflection);
            $this->prepareCode();
        }
        else
        {
            $this->code = base64_decode($code);
        }
        $this->arguments = $this->fetchUsedVariables($this->reflection, $this->code);
        
        if(method_exists($this->reflection, "getClosureThis"))
        {
            $this->source = $this->reflection->getClosureThis();
        }
    }
    
    /**
     * Evaluates the closure and the code in the reset of the file. This sets
     * the code for the closure, the namespace it is declared in (if any) and
     * any applicable namespace imports
     * @param ReflectionFunction The reflected function of the closure
     * @return String The code the closure runs 
     */
    private function evaluate(\ReflectionFunction $reflection)
    {
        $code = '';
        $full = '';
        
        $file = new \SplFileObject($reflection->getFileName());

        while (!$file->eof())
        {
            if($file->key()>=$reflection->getStartLine() - 1 && $file->key() < $reflection->getEndLine())
            {
                $code .= $file->current();
            }
            $full .= $file->current();
            $file->next();
        }
        
        //@todo this assumes the function will be the only one on that line
        $begin = strpos($code, 'function');
        //@todo this assumes the } will be the only one on that line
        $end = strrpos($code, '}');
        
        $this->code = substr($code, $begin, $end - $begin + 1);
        $this->extractDetail($full);
    }
    
    /**
     * Called by evaluate() to determine the declared namespace and class
     * imports applicable to the closure
     * @param string $fileContent
     */
    private function extractDetail($fileContent)
    {
        $imports = array();
        $codeBlocks = token_get_all($fileContent);
        $index = 0;
        foreach($codeBlocks as $c)
        {
            if(is_array($c))
            {
                if($c[0]==T_NAMESPACE)
                {
                    $namespace = $this->getNext(T_STRING, $index, $codeBlocks);
                    if($namespace!=false)
                    {
                        $this->declaredNameSpace = $namespace;
                    }
                }
                if($c[0]==T_USE)
                {
                    $importCandidate = '';
                    
                    for($i = $index+1; $i < count($codeBlocks); $i++)
                    {
                        if(is_array($codeBlocks[$i]))
                        {
                            $importCandidate .= trim($codeBlocks[$i][1]);
                        }
                        else
                        {
                            if(strstr($codeBlocks[$i], ";")!=false)
                            {
                                break;
                            }
                            $importCandidate .= trim($codeBlocks[$i]);
                        }
                    }
                    if(preg_match("/^[a-zA-Z0-9\\\\]+$/", $importCandidate) &&  class_exists($importCandidate))
                    {
                        $imports[] = $importCandidate;
                    }
                }
            }
            $index++;
        }
        
        $this->imports = $imports;
    }
    
    private function getNext($t, $index, $code)
    {
        for($i = $index; $i < count($code); $i++)
        {
            if(is_array($code[$i]) && $code[$i][0]==$t)
            {
                return $code[$i][1];
            }
        }
        return false;
    }
    
    /**
     * Performs string analysis to determin if anything needs to be altered
     * to allow the reconstructed closure to work correctly
     * @todo replace type hints with fq names
     */
    private function prepareCode()
    {
        $nested = array();
        $depth = 0;
        $closureSelf = false;
        $delcaration = false;
        $preparedClosure = "";
        $codeBlocks = token_get_all("<?php $this->code ?>");
        $index = 0;
        
        foreach($codeBlocks as $c)
        {
            $value = '';
            if(is_array($c))
            {
                if($c[0]==T_FUNCTION)
                {
                    if(!$closureSelf)
                    {
                        $closureSelf = true;
                    }
                    else
                    {
                        $delcaration = true;
                        $index = array_push($nested, array('depth' => $depth, 'content' => ''));
                        $preparedClosure .= 'new picon\SerializableClosure(';
                    }
                }
                
                if($c[0]==T_STRING && $this->isNonFullyQualifiedClassName($codeBlocks, $index))
                {
                    $value = $this->resolveToFullyQualifiedClassName($c[1]);
                }
                else
                {
                    $value = $c[1];
                }
            }
            else
            {
                $value = $c;
                
                if($c=="{")
                {
                    $depth++;
                    $delcaration = false;
                }
                else if($c=="}")
                {
                    $depth--;
                }
            }
            $preparedClosure .= $value;
            
            foreach($nested as $index => $function)
            {
                $nested[$index]['content'] .= $value;
                if($function['depth']==$depth && !$delcaration)
                {
                    $preparedClosure .= ', "'.base64_encode($nested[$index]['content']).'")';
                    unset($nested[$index]);
                }
            }
            $index++;
        }
        
        $this->code = substr($preparedClosure, 5, strlen($preparedClosure)-7);
    } 
    
    /**
     * Determins if the code block index provided is a class name that is not
     * fully qualified. This is detected by looking back or forwards
     * up to 2 indexes (to allow for whichspace) and looks for either new or ::
     * @param array $codeBlcoks
     * @param int $currentIndex
     * @return boolean
     */
    private function isNonFullyQualifiedClassName($codeBlcoks, $currentIndex)
    {
        /* Detects class names preceeded by new or succeded by ::
         * If the string is preceeded or succeded by a \ it is alread
         * fully qualified and ignored
         */
        for($i = $currentIndex-2; $i <= $currentIndex+2; $i++)
        {
            if(is_array($codeBlcoks[$i]))
            {
                $c = $codeBlcoks[$i];
                
                if($c[0]==T_NS_SEPARATOR)
                {
                    return false;
                }
                
                if($c[0]==T_NEW && $i < $currentIndex || $c[0]==T_DOUBLE_COLON && $i > $currentIndex)
                {
                    return true; 
                }
            }
        }
        
        /**
         * Determin the index of the ( that starts the closure argument
         * definition
         */
        $workingIndex = 0;
        $search = false;
        $paramOpenIndex = 0;
        while($workingIndex<count($codeBlcoks))
        {
            if(is_array($codeBlcoks[$workingIndex]) && $codeBlcoks[$workingIndex][0]==T_FUNCTION)
            {
                $search = true;
            }
            if(!is_array($codeBlcoks[$workingIndex]) && $search && $codeBlcoks[$workingIndex]=="(")
            {
                $paramOpenIndex = $workingIndex;
            }
            $workingIndex++;
        }
        
        /**
         * Determins if the first ( preceeding the string is the argument
         * declartion, if so this must a type hint
         */
        $trackback = $currentIndex;
        while($trackback>=0)
        {
            if(!is_array($codeBlcoks[$trackback]) && $codeBlcoks[$trackback]=="(" 
                    && $trackback==$paramOpenIndex && $paramOpenIndex!=0)
            {
                return true;
            }
            $trackback--;
        }
        
        return false;
    }
    
    /**
     * Identifies the fully qualified class name (if not already)
     * @param string $className
     * @return string The fully qualified class name
     */
    private function resolveToFullyQualifiedClassName($className)
    {
        if(!strstr($className, "\\"))
        {
            if(!class_exists("\\".$className))
            {
                if(isset($this->declaredNameSpace) && class_exists($this->declaredNameSpace."\\".$className))
                {
                    return $this->declaredNameSpace."\\".$className;
                }
                if(isset($this->imports) && count($this->imports)>0)
                {
                    foreach($this->imports as $import)
                    {
                        $structure = explode("\\", $import);
                        if($structure[count($structure)-1]==$className)
                        {
                            return $import;
                        }
                    }
                }
                throw new \IllegalStateException(sprintf("Unable to locate fully qualified class name for class declared within closure. Offending class %s. Closure: %s", $className, $this->code));

            }
        }   
        return $className;
    }
    
    /**
     * Extract bound variables
     * @param ReflectionFunction The reflected function of the closure
     * @param String The string of code the closure runs
     * @return Array The variable within the use() 
     */
    private function fetchUsedVariables(\ReflectionFunction $reflection, $code)
    {
        $use_index = stripos($code, 'use');
        if (!$use_index)
        {
            return array();
        }

        $begin = strpos($code, '(', $use_index) + 1;
        $end = strpos($code, ')', $begin);
        $vars = explode(',', substr($code, $begin, $end - $begin));

        $static_vars = $reflection->getStaticVariables();

        $used_vars = array();
        foreach ($vars as $var)
        {
            $var = preg_replace("/\\$|\\&/", "", trim($var));
            if(is_callable($static_vars[$var]) && !($static_vars[$var] instanceof SerializableClosure))
            {
                $used_vars[$var] = new SerializableClosure($static_vars[$var]);
            }
            else
            {
                $used_vars[$var] = $static_vars[$var];
            }
        }
        
        return $used_vars;
    }


    /**
     * Validates the closure
     * @param Closure The closure to validate
     */
    private function validateClosure($closure)
    {
        if (!isset($closure) || !($closure instanceof \Closure) || !is_callable($closure))
        {
            throw new \InvalidArgumentException("Closure was not valid");
        }
    }

    public function __sleep()
    {
        return(array('code', 'arguments'));
    }
    
    public function __wakeup()
    {
        extract($this->arguments);
        eval('$closure = '.$this->code.";");
        $this->closure = $closure;
        
        if(method_exists('Closure', 'bind'))
        {
            $this->closure = Closure::bind($this->closure, $this->source, get_class($this->source));
        }
        $this->reflection = new \ReflectionFunction($this->closure);
    }
    
    public function __invoke()
    {
        $args = func_get_args();
        return $this->reflection->invokeArgs($args);
    }
    
    public function getReflection()
    {
        return $this->reflection;
    }
}
?>
