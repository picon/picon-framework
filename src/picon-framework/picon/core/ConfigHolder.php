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
 * Holds the configuration and provides static access to it
 * This will load the configuration once on its first request
 * @todo Turn this into a singleton
 * @author Martin Cassidy
 */
class ConfigHolder
{
    private static $rawConfig = array();
    private static $config;

    /**
     * Look for xml files in the config directory
     * Processes into a config object
     */
    private static function loadConfig()
    {
        self::$config = new Config();
        $configDirectory = BASE_DIRECTORY . "\\config";
        $d = dir($configDirectory);

        while (false !== ($entry = $d->read()))
        {
            if (preg_match("/\\w+.xml{1}/", $entry))
            {
                self::loadXML($configDirectory."\\".$entry);
            }
        }
        $d->close();
        
        $parser = new ConfigParser(self::$rawConfig, self::$config);
        $parser->parse();
    }

    /**
     * Uses an XMLParser to parse the input xml file into an
     * array of XMLTag
     * @param String $xmlFile Path to the xml file
     */
    private static function loadXML($xmlFile)
    {
        $parser = new XMLParser();
        self::$rawConfig = array_merge(self::$rawConfig, $parser->parse($xmlFile));
    }

    /**
     * Get the config 
     * If this is the first invoke for this method, the config will first
     * be loaded and processed.
     * @return The config object
     */
    public static function getConfig()
    {
        if (!isset(self::$config))
        {
            self::loadConfig();
        }
        return self::$config;
    }

}

?>
