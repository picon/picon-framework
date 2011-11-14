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

require_once(PICON_DIRECTORY."\\core\\AutoLoader.php");

/**
 * Extension of autoloader specialised for testings
 * 
 * @author Martin Cassidy
 */
class TestAutoLoader extends \picon\AutoLoader
{
    public function __construct()
    {
        parent::__construct();
        
        $this->addScannedDirectory(PICON_DIRECTORY, 'picon');
        $this->addScannedDirectory(PICON_DIRECTORY."\\annotations");
        $this->addScannedDirectory(PICON_DIRECTORY."\\exceptions");
        $this->addScannedDirectory(__DIR__."\\resources");
        $this->addScannedDirectory(ASSETS_DIRECTORY);
    }
    
    /**
     * Ignores PHPUnit classes which will also pass through this auto loader
     * @param String $className Name of the class to load
     */
    protected function autoLoad($className)
    {
        if(!preg_match("/^PHPUnit+.*$/", $className))
        {
            parent::autoLoad($className);
        }
    }
}

?>
