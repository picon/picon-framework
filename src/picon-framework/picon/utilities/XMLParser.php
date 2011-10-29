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
 * An xml file parser
 * @see http://php.net/manual/en/book.xml.php
 *
 * Parses an XML into an array of XMLTag
 * 
 * @author Martin Cassidy
 */
class XMLParser
{
    private $parser;
    private $stack = array();
    private $depth = 0;
    private $tags = array();
    
    /**
     * Create a new XMLParser
     * Sets up an internal parser including callback methods
     */
    public function __construct()
    {
        $this->parser = xml_parser_create('');
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($this->parser, "self::startElement", "self::endElement");
        xml_set_character_data_handler($this->parser, "self::characterData");
    }
    
    /**
     * Process the XML file into an array of XMLTag objects
     * @param String $xmlFile Path to the XML file
     * @return An array of XMLTag objects
     */
    public function parse($xmlFile)
    {
        if (!($fp = fopen($xmlFile, "r")))
        {
            throw new FileException("Could not open XML input");
        }
        
        while ($data = fread($fp, 4096))
        {
            if (!xml_parse($this->parser, $data, feof($fp)))
            {
                throw new XMLException(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($parser)), xml_get_current_line_number($parser)));
            }
        }
        xml_parser_free($this->parser);
        return $this->tags;
    }
    
    /**
     * Callback for the xml parser when a new element starts
     * @param resource $parser The xml parser
     * @param String $name Name of the element
     * @param Array $attributes array of attributes
     */
    private function startElement($parser, $name, $attributes) 
    {
        $tag = new XMLTag($name, $attributes);
        $this->stack[$this->depth] = $tag;
        
        if($this->depth==0)
        {
            //A root tag
            array_push($this->tags, $tag);
        }
        else
        {
            $this->stack[$this->depth-1]->addChild($tag);
        }
        
        $this->depth++;
    }

    /**
     * Callback for the xml parser when an element ends
     * @param resource $parser The xml parser
     * @param String $name Name of the element
     */
    private function endElement($parser, $name) 
    {
        $this->depth--;
    }
    
    /**
     * Callback for the xml parser when the character data of an element is
     * reached
     * @param resource $parser The xml parser
     * @param String $data The character data
     */
    private function characterData($parser, $data) 
    {
        $this->stack[$this->depth-1]->setCharacterData($data);
    }
}

?>
