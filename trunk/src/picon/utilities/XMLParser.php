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
 * Parses an XML into an array of XMLTag's
 * 
 * @author Martin Cassidy
 * @package utilities
 */
class XMLParser
{
    private $parser;
    private $stack = array();
    private $depth = 0;
    private $root;
    protected $xmlFile;
    private $data;
    
    /**
     * Create a new XMLParser
     * Sets up an internal parser including callback methods
     */
    public function __construct()
    {
        $this->parser = xml_parser_create('UTF-8');
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "startElement", "endElement");
        xml_set_character_data_handler($this->parser, "characterData");
    }
    
    /**
     * Process the XML file into an array of XMLTag objects
     * @param String $xmlFile Path to the XML file
     * @return An array of XMLTag objects
     */
    public function parse($xmlFile)
    {
        $this->xmlFile = $xmlFile;
        if (!($fp = @fopen($xmlFile, "r")))
        {
            throw new \FileException("Could not open XML input");
        }
        while ($data = fread($fp, 4096))
        {
            $this->data = $this->prepare($data);
            if (!xml_parse($this->parser, $this->data, feof($fp)))
            {
                $this->onXmlError(xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser));
            }
        }
        return $this->root;
    }
    
    protected function prepare($data)
    {
        return $data;
    }
    
    /**
     * Callback for the xml parser when a new element starts
     * @param resource $parser The xml parser
     * @param String $name Name of the element
     * @param Array $attributes array of attributes
     */
    private function startElement($parser, $name, $attributes) 
    {
        $tag = $this->newElement($name, $attributes);
        $this->stack[$this->depth] = $tag;
        
        $close = $this->data[$this->getPreceedingCharacter($parser)];
        
        if($close=='/')
        {
            $tag->setTagType(new XmlTagType(XmlTagType::OPENCLOSE));
        }
        
        if($this->depth==0)
        {
            //The root tag
            $this->root = $tag;
        }
        else
        {
            $this->stack[$this->depth-1]->addChild($tag);
        }
        
        $this->depth++;
    }

    protected function newElement($name, $attributes)
    {
        return new XMLTag($name, $attributes);
    }
    
    protected function onXmlError($errorCode, $errorMessage)
    {
        throw new \XMLException(sprintf("XML error: %s at line %d of file %s", $errorCode,$errorMessage, $this->xmlFile));
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
        $this->onCharacterData($data, $this->stack[$this->depth-1]);
    }
    
    protected function onCharacterData($data, XMLTag $element)
    {
        $data = trim($data);
        if(!empty($data))
        {
            $element->addChild(new TextElement($data));
        }
    }
    
    /**
     * Locates a character preceding the &gt; of the start element.
     * This works only when called from startElement and has been altered
     * to allow for differences in the return of xml_get_current_byte_index in 
     * different versions of lib xml
     * 
     * @param XML Parser $parser
     * @return int 
     */
    private function getPreceedingCharacter($parser)
    {
        $index = xml_get_current_byte_index($parser);
        if (isset($this->data[$index])) 
        {
            if($this->data[$index]=='<')
            {
                return strpos(substr($this->data, $index), '>')+$index-1;
            }
            else
            {
                return $index;
            }
        } 
        else 
        {
            $length = strlen($this->data);
            return $length-1;
        }
    }
}

?>
