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

use Exception;

/**
 * Utility class for working with properties of objects
 * 
 * @todo add support for literal getters and setters and not just magic ones
 * @author Martin Cassidy
 */
class PropertyResolver
{
    private function __construct()
    {
        //singleton
    }
    
    public static function hasProperty($object, $propertyName, $reflection = null)
    {
        try
        {
            $target = self::resolveObject($object, $propertyName);
            $property = self::getPropertyForTarget($target, $propertyName);
        }
        catch(Exception $ex)
        {
            return false;
        }
        return true;
    }
    
    public static function get($object, $propertyName)
    {
        $target = self::resolveObject($object, $propertyName);
        $property = self::getPropertyForTarget($target, $propertyName);
        return self::getValue($property, $target);
    }
    
    public static function set($object, $propertyName, &$value)
    {
        $target = self::resolveObject($object, $propertyName);
        $property = self::getPropertyForTarget($target, $propertyName);
        
        if(method_exists($target, '__set'))
        {
            $name = $property->getName();
            $target->$name = $value;
        }
        $property->setAccessible(true);
        
        return $property->setValue($target, $value);
    }
    
    private static function resolveActualProperty($object, $propertyName, $reflection = null)
    {
        if($reflection==null)
        {
            $reflection = new \ReflectionClass($object);
        }
        
        if($reflection->hasProperty($propertyName))
        {
            return $reflection->getProperty($propertyName);
        }
        
        $parent = $reflection->getParentClass();
        if($parent!=null)
        {
            return self::resolveActualProperty($object, $propertyName, $parent);
        }
        return null;
    }
    
    private static function getValue(\ReflectionProperty $property, $object)
    {
        if(method_exists($object, '__get'))
        {
            $name = $property->getName();
            return $object->$name;
        }
        $property->setAccessible(true);
        return $property->getValue($object);
    }
    
    private static function resolveObject($object, $propertyName)
    {
        $target = $object;
        $properties = explode('.', $propertyName);
        
        if(count($properties)==1)
        {
            return $target;
        }
        
        $current = 0;
        while($current<count($properties)-1)
        {
            $property = self::resolveActualProperty($target, $properties[$current]);
            $oldTarget = $target;
            $target = self::getValue($property, $target);
            
            if($target==null)
            {
                throw new \InvalidArgumentException(sprintf("Property %s does not exist in class %s", $properties[$current], get_class($oldTarget)));
            }
            $current++;
        }
        return $target;
    }
    
    private static function getPropertyForTarget($target, $propertyName)
    {
        $properties = explode('.', $propertyName);
        $targetProperty = $properties[count($properties)-1];
        $property = self::resolveActualProperty($target, $targetProperty);
        
        if($property==null)
        {
            throw new \InvalidArgumentException(sprintf("Property %s does not exist in class %s", $targetProperty, get_class($target)));
        }
        return $property;
    }
}

?>
