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
 * 
 * 
 * @author Martin Cassidy
 */
class ClosureSerializationHelper
{
    private function __construct()
    {
        
    }
    
    public static function getSleepingClosure($closure)
    {
        self::validateClosure($closure);
        $reflection = new \ReflectionFunction($closure);
        $code = self::fetchCode($reflection);
        return new SleepingClosure($code, self::fetchUsedVariables($reflection, $code));
    }


    /**
     * Extract the code from the callback as a string
     * @param ReflectionFunction The reflected function of the closure
     * @return String The code the closure runs 
     */
    private static function fetchCode($reflection)
    {
        $file = new \SplFileObject($reflection->getFileName());
        $file->seek($reflection->getStartLine() - 1);

        $code = '';
        while ($file->key() < $reflection->getEndLine())
        {
            $code .= $file->current();
            $file->next();
        }

        $begin = strpos($code, 'function');
        $end = strrpos($code, '}');
        $code = substr($code, $begin, $end - $begin + 1);

        return $code;
    }
    
    /**
     * Extract bound variables
     * @param ReflectionFunction The reflected function of the closure
     * @param String The string of code the closure runs
     * @return Array The variable within the use() 
     */
    private static function fetchUsedVariables($reflection, $code)
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
    private static function validateClosure($closure)
    {
        if (!isset($closure) || get_class($closure)!="Closure" || !is_callable($closure))
        {
            throw new \InvalidArgumentException("Closure was not valid");
        }
    }


    public static function getReconstruction(SleepingClosure $sleepingClosure)
    {
        return '$closure = '.$sleepingClosure->getCode().";";
    }
}

?>
