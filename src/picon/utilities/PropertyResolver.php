<?php

/**
 * Podium CMS
 * http://code.google.com/p/podium/
 *
 * Copyright (C) 2011-2012 Martin Cassidy <martin.cassidy@webquub.com>

 * Podium CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Podium CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Podium CMS.  If not, see <http://www.gnu.org/licenses/>.
 * */

namespace picon;

/**
 * Utility class for working with properties of objects
 * 
 * @tood expand this for getting and setting and implement throughout the 
 * rest of the framework
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
        if($reflection==null)
        {
            $reflection = new \ReflectionClass($object);
        }
        
        if($reflection->hasProperty($propertyName))
        {
            return true;
        }
        
        $parent = $reflection->getParentClass();
        if($parent!=null)
        {
            return self::hasProperty($object, $propertyName, $parent);
        }
    }
}

?>
