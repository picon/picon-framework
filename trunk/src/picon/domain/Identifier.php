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
 * Represents the identity of a class, the class name and the namespace
 *
 * @author Martin
 */
class Identifier extends ComonDomainBase
{
    private $namespace;
    private $className;
    
    private function __construct($namespace, $className)
    {
        $this->namespace = $namespace;
        $this->className = $className;
    }
    
    /**
     *
     * @return string the full name, including namespace, for the Identifier
     */
    public function getFullyQualifiedName()
    {
        return $this->namespace."\\".$this->className;
    }
    
    /**
     * Create a new Identifier for the class of which the given object is an instance of
     * @param object $object
     * @return Identifier
     */
    public static function forObject($object)
    {
        if(!is_object($object))
        {
            throw new \InvalidArgumentException(sprintf("Expected argument 1 to be an object not %s", gettype($object)));
        }
        $reflection = new \ReflectionClass($object);
        return new self($reflection->getNamespaceName(), $reflection->getShortName());
    }
    
    /**
     * Create a new Identifier for a class of given name 
     * @param string $name The name of the class, including the namespace if it has one
     * @return Identifier 
     */
    public static function forName($name)
    {
        if(!class_exists($name) && !interface_exists($name))
        {
            throw new \InvalidArgumentException(sprintf("The class %s is not declared.", $name));
        }
        $reflection = new \ReflectionClass($name);
        return new self($reflection->getNamespaceName(), $reflection->getShortName());
    }
    
    /**
     *
     * @param object $object 
     */
    public function equals($object)
    {
        if(!($object instanceof Identifier))
        {
            return false;
        }
        
        if($object->className==$this->className && $object->namespace==$this->namespace)
        {
            return true;
        }
        return false;
    }
    
    /**
     * Checks whether this identifier is the same or is a child of the given 
     * identifier. Checks for equality, sub class or interface implementation
     * @param Identifier $object The identifier to check against
     * @return boolean  
     */
    public function of($object)
    {
        if(!($object instanceof Identifier))
        {
            return false;
        }
        
        return in_array($this->getFullyQualifiedName(), class_implements($object->getFullyQualifiedName())) || is_subclass_of($this->getFullyQualifiedName(), $object->getFullyQualifiedName()) || $object->getFullyQualifiedName()==$this->getFullyQualifiedName();
    }
}

?>
