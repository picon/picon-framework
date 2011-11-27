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
 * A helper class for loading config xml
 * @author Martin Cassidy
 * @package core
 * @todo Update so that every element found is validated
 * @todo add support for including other xml files allowing larger configs to be split up
 * @todo finish off the parser to process data sources
 * @todo create a schema for the config xml
 */
class ConfigLoader
{
    const ROOT_ELEMENT = "piconApplication";
    const EXTERNAL_INCLUDE = "include";
    private static $CORE_CONFIG = array("homePage", "mode");
    
    private function __construct()
    {
    }
    
    public static function load($file, &$config = null)
    {
        if($config==null)
        {
            $config = new Config();
        }
        $xmlParser = new XMLParser();
        $rawConfig = $xmlParser->parse($file);
        
        ConfigLoader::parse($rawConfig, $config);
        
        return $config;
    }
    
    private static function parse($rawConfig, &$config)
    {
        foreach($rawConfig as $tag)
        {
            if($tag->getName()!=self::ROOT_ELEMENT)
            {
                throw new ConfigException("Unexpected root element ".$tag->getName());
            } 
            foreach($tag->getChildren() as $childTag)
            {
                if(in_array($childTag->getName(), self::$CORE_CONFIG))
                {
                    $name = $childTag->getName();
                    $config->$name = $childTag->getCharacterData();
                }
                
                if($childTag->getName()==self::EXTERNAL_INCLUDE)
                {
                    ConfigLoader::load($childTag->getCharacterData(), $config);
                }
            }
        }
    }
}

?>
