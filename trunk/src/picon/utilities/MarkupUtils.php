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
 * Helper class for finding specific tags in markup
 * 
 * @todo refactor to remove duplicate code
 * @todo alter to return an array if multiple matching tags exist
 * @author Martin Cassidy
 * @package utilities
 */
class MarkupUtils
{
    private function __constructor()
    {
        
    }
    
    public static function findComponentTag($markup, $componentTagId, Component $component)
    {
        if(is_array($markup))
        {
            $componentTag = null;
            foreach($markup as $element)
            {
                self::validateElement($element);
                $componentTag = self::internalFindComponentTag($element, $componentTagId, $component);
                if($componentTag!=null)
                {
                    break;
                }
            }
            return $componentTag;
        }
        else
        {
            self::validateElement($markup);
            return self::findComponentTag($markup->getChildren(), $componentTagId, $component);
        }
    }
    
    private static function validateElement($element)
    {
        if(!($element instanceof XmlElement))
        {
            throw new \InvalidArgumentException(sprintf("Expected XmlElement %s given.", gettype($element)));
        }
    }
    
    private static function internalFindComponentTag(XmlElement $markup, $componentTagId, Component $component)
    {
        if($markup instanceof ComponentTag && $markup->getComponentTagId()==$componentTagId)
        {
            return $markup;
        }
        else
        {
            if(($markup instanceof MarkupElement) && $markup->hasChildren() && (!($markup instanceof ComponentTag) || $component->getId()==$markup->getComponentTagId()))
            {
                $componentTag = null;
                foreach($markup->getChildren() as $element)
                {
                    if($element instanceof MarkupElement)
                    {
                        $componentTag = self::internalFindComponentTag($element, $componentTagId, $component);
                        if($componentTag!=null)
                        {
                            return $componentTag;
                        }
                    }
                }
            }
            return null;
        }
    }
    
    public static function findPiconTag($type, $markup)
    {
        if(is_array($markup))
        {
            $piconTag = null;
            foreach($markup as $element)
            {
                self::validateElement($element);
                $piconTag = self::internalFindPiconTag($element, $type);
                if($piconTag!=null)
                {
                    break;
                }
            }
            return $piconTag;
        }
        else
        {
            self::validateElement($markup);
            return self::internalFindPiconTag($markup, $type);
        }
    }
    
    private static function internalFindPiconTag(MarkupElement $markup, $type)
    {
        if($markup instanceof PiconTag && $markup->getName()=='picon:'.$type)
        {
            return $markup;
        }
        else
        {
            if($markup->hasChildren())
            {
                $piconTag = null;
                foreach($markup->getChildren() as $element)
                {
                    if($element instanceof MarkupElement)
                    {
                        $piconTag = self::internalFindPiconTag($element, $type);
                        if($piconTag!=null)
                        {
                            return $piconTag;
                        }
                    }
                }
            }
            return null;
        }
    }
}

?>
