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
 * Generic helper class for validating method arguments
 * @todo use stack to create a less generic message
 *
 * @author Martin Cassidy
 */
class Args
{
    /**
     * Throws and excpetion if not a callback
     * @param mixed $object The object to test
     */
    public static function callBack($object)
    {
        if(!is_callable($object))
        {
            throw new \InvalidArgumentException("Expecting callable");
        }
    }
    
    /**
     * Throws an exception if the callback does not accept the required number of
     * arguments
     * @param Closure $callback The callback to test
     * @param int $amount The number of arguments the callback should have
     */
    public static function callBackArgs($callback, $amount)
    {
        self::callBack($callback);
        self::isNumeric($amount);
        $reflection = new \ReflectionFunction($callback);
        if(count($reflection->getParameters())!=$amount)
        {
            throw new \InvalidArgumentException(sprintf("Callback should accept 1 argument. Actual %d", count($reflection->getParameters())));
        }
    }
    
    /**
     * Throws an exception if not numeric
     * @param type $number 
     */
    public static function isNumeric($number)
    {
        if(!is_numeric($number))
        {
            throw new \InvalidArgumentException("Expected number");
        }
    }
    
    /**
     * Throws an expection if not of the given instance.
     * If object is null, no excpetion will be thrown
     * @param Object $object
     * @param Identifier $expected
     */
    public static function checkInstance($object, Identifier $expected)
    {
        $fqName = $expected->getFullyQualifiedName();
        if($object!=null && !(is_a($object, $fqName)))
        {
            throw new \InvalidArgumentException(sprintf("Expected type %s, actual %s", $fqName, get_class($object)));
        }
    }
    
    /**
     * Throws an expection if not of the given instance.
     * If object is null, an excpetion will also be thrown
     * @param Object $object
     * @param Identifier $expected
     */
    public static function checkInstanceNotNull($object, Identifier $expected)
    {
        if($object==null)
        {
            throw new \InvalidArgumentException("Expected non null");
        }
        else
        {
            self::checkInstance($object, $expected);
        }
    }
    
    public static function isArray($object)
    {
        if(!is_array($object))
        {
            throw new \InvalidArgumentException("Expected array");
        }
    }
}

?>
