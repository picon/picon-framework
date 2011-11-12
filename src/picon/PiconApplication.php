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
require_once("core/ApplicationInitialiser.php");

require_once("addendum/annotation_parser.php");
require_once("addendum/annotations.php");
require_once("addendum/doc_comment.php");

/**
 * Path to the root picon directory without a trailing slash
 */
define("BASE_DIRECTORY", __DIR__."\\..");

/**
 * Path to the root picon directory without a trailing slash
 * This is the directory containing PiconApplication
 */
define("PICON_DIRECTORY", __DIR__);

/**
 * Path to the assets directory in which the user
 * created classes reside
 */
define("ASSETS_DIRECTORY", BASE_DIRECTORY.'\\assets');

/**
 * This is the main driver class for the entire application
 *
 * @author Martin Cassidy
 */
class PiconApplication 
{
    /**
     * Fires off the application initialiser to load an instantiat all resources
     */
    public function __construct()
    {
        $initialiser = new ApplicationInitialiser();
        $initialiser->addScannedDirectory(PICON_DIRECTORY, 'picon');
        $initialiser->addScannedDirectory(PICON_DIRECTORY."\\annotations");
        $initialiser->addScannedDirectory(PICON_DIRECTORY."\\exceptions");
        $initialiser->addScannedDirectory(ASSETS_DIRECTORY);
        $initialiser->initialise();
    }
    
    public function run()
    {
        
    }
}

?>
