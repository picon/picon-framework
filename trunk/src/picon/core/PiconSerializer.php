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
 * Extends the standard PHP serialization functionality by permiting
 * serialisazation of closures, adds support for transient properties
 * 
 * Use PiconSerializer::serialize() and unserialize::serialize() instead of
 * normal PHP serialize() and unserialize(). __sleep() and __wakeup will
 * be called in the normal way BUT should not rely on Transient properties being present
 * or closures being callable
 * 
 * Transient propertie can be used by adding @Transient to the property's 
 * doc block
 * 
 * If using injection in a subclass, you should implement InjectOnWakeup
 * to ensure injected resources are not null as all injected resources
 * are transient.
 * 
 * 
 * 
 * @author Martin Cassidy
 * @package core
 */
class PiconSerializer
{
    private static $prepared = array();
    
    public static function serialize($object)
    {
        $reflection = new \ReflectionAnnotatedClass($object);
        self::$prepared = array();
        self::preparForSerialize($reflection, $object);
        self::$prepared = array();
        return serialize($object);
    }
    
    public static function unserialize($serialized)
    {
        $target = $serialized;
        if(is_string($serialized))
        {
            $target = unserialize($serialized);
        }

        $reflection = new \ReflectionAnnotatedClass($target);
        self::$prepared = array();
        self::reformOnUnserialize($reflection, $target);
        self::$prepared = array();
        return $target;
    }
    
    /**
     * Uses reflection to analyse the properties of the object and prevents
     * transient properties from being added to the array and deconstructs
     * closures into a SleepingClosure object which can be serialized
     * @return array the properties to serialize, the name of the propety as the key with the value as the value
     */
    private static function preparForSerialize(\ReflectionAnnotatedClass $reflection, &$target, $parent = false)
    {
        $hash = spl_object_hash($target);
        
        if(in_array($hash, self::$prepared) && $parent==false)
        {
            return;
        }
        array_push(self::$prepared, $hash);
        foreach($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            $value = $property->getValue($target);

            if(self::isTransient($property))
            {
                $property->setValue($target, null);
            }
            elseif(is_object($value) && is_callable($value) && !($value instanceof SerializableClosure))
            {
                $property->setValue($target, new SerializableClosure($value));
            }
            elseif(is_object($value) && spl_object_hash($value)!=$hash)
            {
                $objectReflection = new \ReflectionAnnotatedClass($value);
                self::preparForSerialize($objectReflection, $value);
            }
            elseif(is_array($value))
            {
                self::prepareArrayForSerialize($value);
            }
        }
        
        $parent = $reflection->getParentClass();
        
        if($parent!=null)
        {
            $parentValues = self::preparForSerialize($parent, $target, true);
        }
    }
    
    private static function prepareArrayForSerialize(&$entry)
    {
        foreach($entry as $value)
        {
            if(is_array($value))
            {
                self::prepareArrayForSerialize($value);
            }
            else if(is_object($value))
            {
                $objectReflection = new \ReflectionAnnotatedClass($value);
                self::preparForSerialize($objectReflection, $value);
            }
        }
    }
    
    private static function reformOnUnserialize(\ReflectionAnnotatedClass $reflection, &$target, $parent = false)
    {
        $hash = spl_object_hash($target);
        
        if(in_array($hash, self::$prepared) && $parent==false)
        {
            return;
        }
        array_push(self::$prepared, $hash);
        
        if($target instanceof InjectOnWakeup)
        {
            Injector::get()->inject($target);
        }
        
        $defaults = $reflection->getDefaultProperties();
        foreach($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            $value = $property->getValue($target);
            if(self::isTransient($property))
            {
                $property->setValue($target, $defaults[$property->getName()]);
            }
            else if($value instanceof SerializableClosure)
            {
                $value->bind($target);
            }
            else if(is_object($value))
            {
                $objectReflection = new \ReflectionAnnotatedClass($value);
                self::reformOnUnserialize($objectReflection, $value);
            }
            else if(is_array($value))
            {
                self::reformArray($value);
            }
        }

        $parent = $reflection->getParentClass();
        if($parent!=null)
        {
            self::reformOnUnserialize($parent, $target, true);
        }
    }
    
    private static function reformArray(&$entry)
    {
        foreach($entry as $value)
        {
            if(is_array($value))
            {
                self::reformArray($value);
            }
            else if(is_object($value))
            {
                $objectReflection = new \ReflectionAnnotatedClass($value);
                self::reformOnUnserialize($objectReflection, $value);
            }
        }
    }
    
    private static function isTransient(\ReflectionAnnotatedProperty $property)
    {
        foreach($property->getAllAnnotations() as $annotation)
        {
            if($annotation instanceof \Transient)
            {
                return true;
            }
        }
        return false;
    }
}

?>
