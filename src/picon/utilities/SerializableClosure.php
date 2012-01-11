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
 * @todo imporove so that  all type hinting usage of classes within a closure don't need to be fully qualified
 * @package utilities
 * @author Martin Cassidy
 */
class SerializableClosure
{
    private $closure;
    private $code;
    private $arguments;
    private $source;
    private $reflection;
    
    public function __construct($closure, $code = null)
    {
        $this->validateClosure($closure);
        $this->reflection = new \ReflectionFunction($closure);
        
        if($code==null)
        {
            $this->code = $this->fetchCode($this->reflection);
            $this->prepareCode();
        }
        else
        {
            $this->code = base64_decode($code);
        }
        $this->arguments = $this->fetchUsedVariables($this->reflection, $this->code);
    }
    
    /**
     * Extract the code from the callback as a string
     * @param ReflectionFunction The reflected function of the closure
     * @return String The code the closure runs 
     */
    private function fetchCode(\ReflectionFunction $reflection)
    {
        $code = null;
        $file = new \SplFileObject($reflection->getFileName());
        $file->seek($reflection->getStartLine() - 1);

        $code = '';
        while ($file->key() < $reflection->getEndLine())
        {
            $code .= $file->current();
            $file->next();
        }

        //@todo this assumes the function will be the only one on that line
        $begin = strpos($code, 'function');
        //@todo this assumes the } will be the only one on that line
        $end = strrpos($code, '}');
        $code = substr($code, $begin, $end - $begin + 1);
        return $code;
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
                $value = $c[1];
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
        }
        $preparedClosure = substr($preparedClosure, 5, strlen($preparedClosure)-7);
        
        //function{1}\s*\({1}(\w*\s*&?\${1}\w+)*\){1}
        $this->code = $preparedClosure;
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
            $var = trim($var, ' $&amp;');
            $used_vars[$var] = $static_vars[$var];
        }
        return $used_vars;
    }


    /**
     * Validates the closure
     * @param Closure The closure to validate
     * @todo Figure out why instanceof Clousre fails
     */
    private function validateClosure($closure)
    {
        if (!isset($closure) || get_class($closure)!="Closure" || !is_callable($closure))
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
        $this->reflection = new \ReflectionFunction($this->closure);
    }
    
    /**
     * @todo test in 5.4
     * @param type $object 
     */
    public function bind(&$object)
    {
        if(method_exists('Closure', 'bind'))
        {
            $this->source = $object;
            $this->closure = Closure::bind($this->closure, $this->source, get_class($this->source));
        }
    }
    
    public function __invoke()
    {
        if(method_exists('Closure', 'bind') && $this->source==null)
        {
            throw new \IllegalStateException("A serialized closure cannot be invoked after deserialization until it has been bound");
        }
        $args = func_get_args();
        return $this->reflection->invokeArgs($args);
    }
    
    public function getReflection()
    {
        return $this->reflection;
    }
}
?>
