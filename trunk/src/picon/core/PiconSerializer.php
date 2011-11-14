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
 * serialisazation of closures and adds support for transient properties
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
 * @author Martin Cassidy
 * @package core
 */
class PiconSerializer
{
    /**
     * Uses reflection to analyse the properties of the object and prevents
     * transient properties from being added to the array and deconstructs
     * closures into a SleepingClosure object which can be serialized
     * @return array the names of the properties of this object to serialize
     */
    public function __sleep()
    {
        $serializable = array();
        $reflection = new \ReflectionAnnotatedClass($this);
        
        foreach($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            if($this->isTransient($property))
            {
                //do nothing
            }
            elseif(is_callable($property->getValue($this)))
            {
                $property->setValue($this, ClosureSerializationHelper::getSleepingClosure($property->getValue($this)));
                array_push($serializable, $property->getName());
            }
            else
            {
                array_push($serializable, $property->getName());
            }
        }
        return $serializable;
    }
    
    /**
     * Restores any transient properties to thier default value
     * Reconstructs closures to their origional selves
     * Re injects if needed (if implements InjectOnWakeup)
     */
    public function __wakeup()
    {
        $reflection = new \ReflectionAnnotatedClass($this);
        $defaults = $reflection->getDefaultProperties();
        foreach($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            if($this->isTransient($property))
            {
                $property->setValue($this, $defaults[$property->getName()]);
            }
            elseif($property->getValue($this) instanceof SleepingClosure)
            {
                $sleeping = $property->getValue($this);
                extract($sleeping->getUsedVars());
                eval(ClosureSerializationHelper::getReconstruction($sleeping));
                $property->setValue($this, $closure);
            }
            else
            {
                //do nothing
            }
        }
        if($this instanceof InjectOnWakeup)
        {
            Injector::get()->inject($this);
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
