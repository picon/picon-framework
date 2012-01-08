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
 * This class should be extended to take advantage of its features. It is
 * imperative that be sub classes are serialized the preparForSerialize() be
 * called. The picon cache manager will do this automatically but manual serialization
 * elsewhere must call the method. Deserailization is handled automatically through
 * the __wakeup method and any overrides MUST call the parent implementation
 * 
 * Transient propertie can be used by adding @Transient to the property's 
 * doc block
 * 
 * If using injection in a subclass, you should implement InjectOnWakeup
 * to ensure injected resources are not null as all injected resources
 * are transient.
 * 
 * @author Martin Cassidy
 * @package cache
 */
class PiconSerializable
{
    private static $prepared = array();
    
    /**
     * Uses reflection to analyse the properties of the object and prevents
     * transient properties from being added to the array and deconstructs
     * closures into a SleepingClosure object which can be serialized
     * @return array the properties to serialize, the name of the propety as the key with the value as the value
     */
    public function preparForSerialize()
    {
        $reflection = new \ReflectionAnnotatedClass($this);
        self::$prepared = array();
        $this->internalPrepareForSerialize($reflection);
        self::$prepared = array();
    }
    
    private function internalPrepareForSerialize(\ReflectionAnnotatedClass $reflection, $parent = false)
    {
        $hash = spl_object_hash($this);
        
        if(in_array($hash, self::$prepared) && $parent==false)
        {
            return;
        }
        array_push(self::$prepared, $hash);
        foreach($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            $value = $property->getValue($this);

            if($this->isTransient($property))
            {
                $property->setValue($this, null);
            }
            elseif(is_object($value) && is_callable($value) && !($value instanceof SerializableClosure))
            {
                $property->setValue($this, new SerializableClosure($value));
            }
            elseif(is_object($value) && spl_object_hash($value)!=$hash && $value instanceof PiconSerializable)
            {
                $valueReflection = new \ReflectionAnnotatedClass($value);
                $value->internalPrepareForSerialize($valueReflection);
            }
            elseif(is_array($value))
            {
                $this->prepareArrayForSerialize($value);
            }
        }
        
        $parent = $reflection->getParentClass();
        
        if($parent!=null)
        {
            $parentValues = $this->internalPrepareForSerialize($parent, $this, true);
        }
        
        if($this instanceof Detachable)
        {
            $this->detach();
        }
    }
    
    private function prepareArrayForSerialize(&$entry)
    {
        foreach($entry as $value)
        {
            if(is_array($value))
            {
                $this->prepareArrayForSerialize($value);
            }
            else if(is_object($value) && $value instanceof PiconSerializable)
            {
                $valueReflection = new \ReflectionAnnotatedClass($value);
                $value->internalPrepareForSerialize($valueReflection);
            }
        }
    }
    
    public function __wakeup()
    {
        $reflection = new \ReflectionAnnotatedClass($this);
        self::$prepared = array();
        self::reformOnUnserialize($reflection);
        self::$prepared = array();
    }
    
    private function reformOnUnserialize(\ReflectionAnnotatedClass $reflection, $parent = false)
    {
        $hash = spl_object_hash($this);
        
        if(in_array($hash, self::$prepared) && $parent==false)
        {
            return;
        }
        array_push(self::$prepared, $hash);
        
        if($this instanceof InjectOnWakeup)
        {
            Injector::get()->inject($this);
        }
        
        $defaults = $reflection->getDefaultProperties();
        foreach($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            $value = $property->getValue($this);
            if(self::isTransient($property))
            {
                $property->setValue($this, $defaults[$property->getName()]);
            }
            else if($value instanceof SerializableClosure)
            {
                $value->bind($this);
            }
            else if(is_object($value))
            {
                $objectReflection = new \ReflectionAnnotatedClass($value);
                $this->reformOnUnserialize($objectReflection);
            }
            else if(is_array($value))
            {
                $this->reformArray($value);
            }
        }

        $parent = $reflection->getParentClass();
        if($parent!=null)
        {
            $this->reformOnUnserialize($parent, true);
        }
    }
    
    private function reformArray(&$entry)
    {
        foreach($entry as $value)
        {
            if(is_array($value))
            {
                $this->reformArray($value);
            }
            else if(is_object($value))
            {
                $objectReflection = new \ReflectionAnnotatedClass($value);
                $this->reformOnUnserialize($objectReflection);
            }
        }
    }
    
    private function isTransient(\ReflectionAnnotatedProperty $property)
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
