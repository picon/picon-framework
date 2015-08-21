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

namespace picon\context;

/**
 * Dependency injector for resources. All resources are inject after instantiation.
 * Although any object can be manually injected at any time.
 * 
 * @author Martin Cassidy
 * @package context
 */
class Injector
{
    private $context;
    private static $injector;
    
    public function __construct(ApplicationContext $context)
    {
        $this->context = $context;
        Injector::$injector = $this;
    }
    
    /**
     * @param $object
     */
    public function inject(&$object, $reflection = null)
    {
        if($reflection==null)
        {
            $reflection = new \ReflectionAnnotatedClass($object);
        }
        $properties = $reflection->getProperties();
                
        foreach($properties as $property)
        { 
            if($property->hasAnnotation("Resource"))
            {
                $annotation = $property->getAnnotation("Resource");
                $resource = $property->getName();
                
                if(!empty($annotation->name))
                {
                    $resource = $annotation->name;
                }
                
                $property->setAccessible(true);
                $property->setValue($object, $this->context->getResource($resource));
            }
        }
        
        $parent = $reflection->getParentClass();
        if($parent!=null)
        {
            $this->inject($object, $parent);
        }
    }
    
    public static function get()
    {
        return Injector::$injector;
    }
}

?>
