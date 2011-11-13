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
 * Holder for the map to all web pages found in the assets directory.
 * That is all pages which are sub classes of picon\WebPage
 * 
 * @author Martin Cassidy
 * @package core
 */
class PageMapHolder
{
    private $map = array();
    private static $self;
    
    /**
     * Private constructor, this is a singleton
     * Loads in ALL .php files from the assets directory
     * Builds the page map array
     */
    private function __construct()
    {
        $classes = get_declared_classes();
        
        foreach($classes as $class)
        {
             $reflection = new \ReflectionClass($class);
             if($reflection->isSubclassOf("\picon\WebPage"))
             {
                 array_push($this->map, $class);
             }
        }
    }
    
    /**
     * Gets the array containing the page map
     * If this is the first invoke for this method, the page map will
     * be generated
     * @return Array The page map
     */
    public static function getPageMap()
    {
        if(!isset($self))
        {
            self::$self = new self();
        }
        return self::$self->map;
    }
}

?>
