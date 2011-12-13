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
 * @package domain
 */
class XMLTag extends ComonDomainBase implements XmlElement
{
    private $name;
    private $tagType;
    private $attributes = array();
    private $children = array();
    
    /**
     * Construct a new xml tag
     * @param String $name the name of the tag
     * @param Array $attributes optional, defaults to an empty array
     */
    public function __construct($name, $attributes = array())
    {
        $this->name = $name;
        $this->attributes = $attributes;
        
        //Openclose by default, this is because of the way the xml parser works
        $this->tagType = new XmlTagType(XmlTagType::OPENCLOSE);
    }
    
    /**
     * Add a new XMLTag child
     * @param XMLTag $child the child to add
     */
    public function addChild(XmlElement $child)
    {
        array_push($this->children, $child);
    }
    
    /**
     * Set the name of this tag
     * @param String the name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Sets the attributes
     * @param Array the attributes of this tag
     */
    public function setAttributes($attributes)
    {
        if(!is_array($attributes))
        {
            throw new \InvalidArgumentException(sprintf("Expected array, %s given.", gettype($attributes)));
        }
        $this->attributes = $attributes;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Does this tag have any children
     * @return boolean true if there are children 
     */
    public function hasChildren()
    {
        return count($this->children)>0;
    }
    
    /**
     * Set the type of tag this is: open, close, openclose
     * @param XmlTagType The type
     */
    public function setTagType(XmlTagType $type)
    {
        $this->tagType = $type;
    }
    
    public function isOpen()
    {
        return $this->tagType->equals(new XmlTagType(XmlTagType::OPEN));
    }
    
    public function isClose()
    {
        return $this->tagType->equals(new XmlTagType(XmlTagType::CLOSED));
    }
    
    public function isOpenClose()
    {
        return $this->tagType->equals(new XmlTagType(XmlTagType::OPENCLOSE));
    }
    
    public function put($name, $value)
    {
        $this->attributes[$name] = $value;
    }
    
    /**
     * Remove an attribute
     * @param type $name 
     */
    public function remove($name)
    {
        if(array_key_exists($name, $this->attributes))
        {
            unset($this->attributes[$name]);
        }
    }
    
    /**
     *
     * @param array $children 
     */
    public function setChildren($children)
    {
        Args::isArray($children, 'children');
        $this->children = $children;
    }
    
    public function getCharacterData()
    {
        $data = "";
        foreach($this->children as $child)
        {
            if($child instanceof TextElement)
            {
                $data .= $child->getContent();
            }
        }
        return $data;
    }
}

?>
