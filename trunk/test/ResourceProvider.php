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
 **/


/**
 * Same constants as defined normally by index.php
 */
define("PICON_DIRECTORY", __DIR__.'\\..\\src\\picon');
define("ASSETS_DIRECTORY", __DIR__.'\\..\\src\\assets');
define("CONFIG_FILE", __DIR__.'\\config\\picon.xml');

require_once("../src/picon/PiconApplication.php");

/**
 * Provider for test resources
 */
class ResourceProvider
{
    private $application;
    private static $self;
    
    private function __construct()
    {
        $this->application = new picon\PiconApplication();
    }
    
    /**
     * If needed, instantiates the ResourceProvider, then returns it
     * @return ResourceProvider the resource provider
     */
    public static function get()
    {
        if(!isset(ResourceProvider::$self))
        {
            ResourceProvider::$self = new self();
        }
        return ResourceProvider::$self;
    }
    
    public function getApplication()
    {
        return $this->application;
    }
}

?>
