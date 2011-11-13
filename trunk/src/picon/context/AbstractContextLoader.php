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
 * Context loader super class
 * 
 * @author Martin Cassidy
 * @package context
 */
abstract class AbstractContextLoader
{
    private $resources = array();
    
    public function load()
    {
        $this->loadResources($this->getClasses());
        return new ApplicationContext($this->resources);
    }
    
    protected abstract function loadResources($classes);
    
    protected function pushToResourceMap($resourceName, $resource)
    {
        $this->resources[$resourceName] = $resource;
    }
    
    /**
     * Gets the resource name for an object. By default this is the class
     * name (with a lowercase first letter e.g. class MyResource is named
     * myResource) If name has been specified in the annotation then
     * the name is extracted from there instead.
     * @param type $annotation
     * @param type $className
     * @return type 
     */
    protected function getResourceName(\Annotation $annotation, $className)
    {
        $name = $annotation->value["name"];
        
        if($name=="")
        {
            return strtolower(substr($className, 0, 1)).substr($className,1,strlen($className));
        }
        else
        {
            return $name;
        }
    }
    
    public static function getClasses()
    {
        return get_declared_classes();
    }
}

?>
