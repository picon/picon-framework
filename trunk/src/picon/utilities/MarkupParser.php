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
 * Parser for picon XML. This works like the XMLParser but will produce
 * different XMLTag objects for different picon elements
 * 
 * MarkupElement - A normal HTML element
 * ComponentTag - An HTML element with a picon:id that will have a corrisponding
 * component in the hierachy
 * PiconTag - A special picon tag such as picon:child or picon:panel
 * StringElement - Holds the character data as a child of the xml tag
 * 
 * @author Martin Cassidy
 * @package utilities
 * @todo add support for extracting the doctype
 */
class MarkupParser extends XMLParser
{
    private static $PICON_ELEMENTS = array('child', 'extend', 'panel');
    
    protected function newElement($name, $attributes)
    {
        if(array_key_exists('picon:id', $attributes))
        {
            return new ComponentTag($name, $attributes);
        }
        elseif(in_array(str_replace('picon:', '', $name), MarkupParser::$PICON_ELEMENTS))
        {
            return new PiconTag($name, $attributes);
        }
        else
        {
            return new MarkupElement($name, $attributes);
        }
    }
    
    protected function onXmlError($errorCode, $errorMessage)
    {
        throw new \InvalidMarkupException(sprintf("XML error: %s at line %d of file %s", $errorCode,$errorMessage, $this->xmlFile));
    }
    
    protected function onCharacterData($data, $element)
    {
        $element->addChild(new StringElement($data));
    }
}

?>
