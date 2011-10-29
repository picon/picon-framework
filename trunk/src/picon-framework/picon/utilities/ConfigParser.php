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
 * Description of ConfigParser

 * @author Martin Cassidy
 */
class ConfigParser
{
    const ROOT_ELEMENT = "piconApplication";
    
    private $config;
    private $rawConfig;
    
    private static $CORE_CONFIG = array("homePage", "mode");
    
    public function __construct(&$rawConfig, &$config)
    {
        $this->config = &$config;
        $this->rawConfig = &$rawConfig;
    }
    
    public function parse()
    {
        foreach($this->rawConfig as $tag)
        {
            if($tag->getName()!=self::ROOT_ELEMENT)
            {
                throw new ConfigException("Unexpected root element ".$tag->getName());
            } 
            foreach($tag->getChildren() as $childTag)
            {
                if(in_array($childTag->getName(), self::$CORE_CONFIG))
                {
                    $this->dealCore($childTag->getName(), $childTag->getCharacterData());
                }
            }
        }
    }
    
    public function dealCore($name, $value)
    {
        $this->config->__set($name, $value);
    }
}

?>
