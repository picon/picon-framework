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

namespace picon\core\cache;

//@todo remove this coupling to web
use picon\context\Injector;
use picon\core\InjectOnWakeup;
use picon\core\utils\SerializableClosure;
use picon\web\Detachable;

/**
 * A serialization helper that extends the standard PHP serialization 
 * functionality by permiting serialisazation of closures and providing
 * support for transient properties.
 *
 * @author Martin Cassidy
 * @package cache
 */
class PiconSerializer
{
    private static $prepared = array();
    private static $restore = array();
    
    private function __construct()
    {
        //Singleton
    }
    
    /**
     * serialize an object
     * @param mixed $object
     * @return string 
     */
    public static function serialize($object)
    {
        if(!is_object($object) && (!is_callable($object) && !($object instanceof \Closure)))
        {
            return serialize($object);
        }
        
        if($object instanceof \Closure)
        {
            return serialize(new SerializableClosure($object));
        }
        
        self::$prepared = array();
        self::$restore = array();
        self::prepareForSerize(new \ReflectionAnnotatedClass($object), $object);
        
        $serialized = serialize($object);
        self::restore();
        
        self::$prepared = array();
        self::$restore = array();
        return $serialized;
    }
    
    /**
     * Unserialize an object
     * @param string $string
     * @return mixed 
     */
    public static function unserialize($string)
    {
        $unserialzed = unserialize($string);
        
        if($unserialzed instanceof InjectOnWakeup)
        {
            Injector::get()->inject($unserialzed);
        }
        
        return $unserialzed;
    }
    
    private static function prepareForSerize(\ReflectionAnnotatedClass $reflection, $object, $parent = false)
    {
        $hash = spl_object_hash($object);
        if(in_array($hash, self::$prepared) && $parent==false)
        {
            return;
        }
        array_push(self::$prepared, $hash);
        $globalAltered = false;
        $defaults = null;
        
        foreach($reflection->getProperties() as $property)
        {
            $altered = false;
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if(self::isTransient($property))
            {
                $defaults = $defaults==null?$reflection->getDefaultProperties():$defaults;
                $name = $property->getName();
                $property->setValue($object, array_key_exists($name, $defaults)?$defaults[$name]:null);
                $altered = true;
            }
            else if(is_object($value) && $value instanceof \Closure)
            {
                $property->setValue($object, new SerializableClosure($value));
                $altered = true;
            }
            else if(is_object($value) && !($value instanceof SerializableClosure) && spl_object_hash($value)!=$hash)
            {
                $valueReflection = new \ReflectionAnnotatedClass($value);
                $altered = self::prepareForSerize($valueReflection, $value);
            }
            else if(is_array($value))
            {
                $newValue = self::prepareArrayForSerialize($value);
                
                if(is_array($newValue))
                {
                    $property->setValue($object, $newValue);
                    $altered = true;
                }
            }
            
            if($altered)
            {
                self::addRestore($property, $object, $value);
                $globalAltered = true;
            }
        }
        
        $parent = $reflection->getParentClass();
        
        if($parent!=null)
        {
            self::prepareForSerize($parent, $object, true);
        }
        
        if(!$parent)
        {
            if($object instanceof Detachable)
            {
                $object->detach();
            }
        }
        return $globalAltered;
    }
    
    /**
     * @todo This should handle array recursion
     * @param array $entry
     * @return SerializableClosure 
     */
    private static function prepareArrayForSerialize($entry)
    {
        $newEntry = array();
        $altered = false;
        
        foreach($entry as $key => $value)
        {
            if(is_array($value))
            {
                $newValue = self::prepareArrayForSerialize($value);
                $newEntry[$key] = is_array($newValue) ? $newValue : $value;
                $altered = $altered?true:is_array($newValue);
            }
            else if(is_object($value) && $value instanceof \Closure)
            {
                $newEntry[$key] = new SerializableClosure($value);
                $altered = true;
            }
            else if(is_object($value) && !($value instanceof SerializableClosure) && !in_array(spl_object_hash($value), self::$prepared))
            {
                $ia = self::prepareForSerize(new \ReflectionAnnotatedClass($value), $value);
                $altered = $altered?true:$ia;
                $newEntry[$key] = $value;
            }
            else
            {
                $newEntry[$key] = $value;
            }
        }
        
        if($altered)
        {
            return $newEntry;
        }
        return false;
    }
    
    private static function addRestore(\ReflectionAnnotatedProperty $property, $object, $value)
    {
        $restore = new \stdClass();
        $restore->property = $property;
        $restore->object = $object;
        $restore->value = $value;
        self::$restore[] = $restore;
    }
    
    private static function restore()
    {
        foreach(self::$restore as $restore)
        {
            $restore->property->setValue($restore->object, $restore->value);
        }
    }
    
    private static function isTransient(\ReflectionAnnotatedProperty $property)
    {
        $annotations = $property->getAllAnnotations();
        
        foreach($annotations as $annotation)
        {
            return is_subclass_of($annotation, "picon\core\annotations\Transient") || get_class($annotation)=="picon\core\annotations\Transient";
        }
    }
}
