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
 * serialisazation of closures, adds support for transient properties and
 * allows __sleep to return private properties from parent classes
 * 
 * Any class which requires complex serialization should extend this
 * 
 * Transient propertie can be used by adding @Transient to the property's 
 * doc block
 * 
 * If using injection in a subclass, you should implement InjectOnWakeup
 * to ensure injected resources are not null as all injected resources
 * are transient.
 * 
 * @todo Add support for recursion in objects. Currenrtly this will fall over
 * as it does not properly support object references. Presently the sub class
 * is required to implement __sleep and ensure any properties which will recur are not
 * returned and and __wakeup to restore the value
 * 
 * @author Martin Cassidy
 * @package core
 */
class PiconSerializer implements \Serializable
{
    public function serialize()
    {
        $properties = array();
        if(method_exists($this, '__sleep'))
        {
            $properties = $this->__sleep();
        }
        else
        {
            $properties = $this->getProperties();
        }
        Args::isArray($properties);
        $reflection = new \ReflectionAnnotatedClass($this);
        return serialize($this->gather($reflection, $properties));
    }
    
    public function unserialize($serialized)
    {
        $properties = unserialize($serialized);
        $reflection = new \ReflectionAnnotatedClass($this);
        $this->distribute($reflection, $properties);
        if($this instanceof InjectOnWakeup)
        {
            Injector::get()->inject($this);
        }
        
        if(method_exists($this, '__wakeup'))
        {
            $properties = $this->__wakeup();
        }
    }
    
    /**
     * Uses reflection to analyse the properties of the object and prevents
     * transient properties from being added to the array and deconstructs
     * closures into a SleepingClosure object which can be serialized
     * @return array the properties to serialize, the name of the propety as the key with the value as the value
     */
    private function gather(\ReflectionAnnotatedClass $reflection, $properties)
    {
        $serializable = array();
        foreach($reflection->getProperties() as $property)
        { 
            if(in_array($property->getName(), $properties))
            {
                $property->setAccessible(true);
                if($this->isTransient($property))
                {
                    //do nothing
                }
                elseif(is_callable($property->getValue($this)))
                {
                    $serializable[$property->getName()] = ClosureSerializationHelper::getSleepingClosure($property->getValue($this));
                    $property->setValue($this, '');
                }
                else
                {
                    $serializable[$property->getName()] = $property->getValue($this);
                }
            }
        }
        
        $parent = $reflection->getParentClass();
        if($parent->getShortName()!='PiconSerializer')
        {
            $parentValues = $this->gather($parent, $properties);
            foreach($parentValues as $name => $value)
            {
                if(!array_key_exists($name, $serializable))
                {
                    $serializable[$name] = $value;
                }
            }
        }
        return $serializable;
    }
    
    private function distribute(\ReflectionAnnotatedClass $reflection, $properties)
    {
        $defaults = $reflection->getDefaultProperties();
        foreach($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            if($this->isTransient($property))
            {
                $property->setValue($this, $defaults[$property->getName()]);
            }
            if(array_key_exists($property->getName(), $properties))
            {
                $propertyValue = $properties[$property->getName()];

                if($propertyValue instanceof SleepingClosure)
                {
                    extract($propertyValue->getUsedVars());
                    eval(ClosureSerializationHelper::getReconstruction($propertyValue));
                    $property->setValue($this, $closure);
                }
                else
                {
                    $property->setValue($this, $propertyValue);
                }
            }
        }
        
        $parent = $reflection->getParentClass();
        if($parent->getShortName()!='PiconSerializer')
        {
            $this->distribute($parent, $properties);
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
    
    protected function getProperties(\ReflectionAnnotatedClass $reflection = null)
    {
        if($reflection==null)
        {
            $reflection = new \ReflectionAnnotatedClass($this);
        }
        $properties = array();
        foreach($reflection->getProperties() as $property)
        {
            array_push($properties, $property->getName());
        }
        
        $parent = $reflection->getParentClass();
        if($parent->getShortName()!='PiconSerializer')
        {
            $parentProperties = $this->getProperties($parent);
            $properties = array_merge($parentProperties, array_diff($properties, $parentProperties));
        }
        return $properties;
    }
}

?>
