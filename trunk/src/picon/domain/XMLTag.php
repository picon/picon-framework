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
 * Class representing an XML tag
 *
 * @author Martin Cassidy
 */
class XMLTag extends ComonDomainBase
{
    private $name;
    private $attributes;
    private $children = array();
    private $characterData;
    
    /**
     * Construct a new xml tag
     * @param String $name the name of the tag
     * @param Array $attributes optional, defaults to an empty array
     */
    public function __construct($name, $attributes = array())
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }
    
    /**
     * Add a new XMLTag child
     * @param XMLTag $child the child to add
     */
    public function addChild(XMLTag $child)
    {
        array_push($this->children, $child);
    }
    
    public function setCharacterData($characterData)
    {
        $this->characterData = $characterData;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getCharacterData()
    {
        return $this->characterData;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
}

?>
