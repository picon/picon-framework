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
 * This is the main class for the entire application.
 *
 * @author Martin Cassidy
 */
class PiconApplication 
{
    private $applicatoinContext;
    private $config;
    private $requestProcessor;
    
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
        $initialiser->initialise($this);
        $GLOBALS['application'] = $this;
    }
    
    public final function run()
    {
        $this->requestProcessor = new RequestCycle();
        $this->requestProcessor->process();
    }
    
    public final function getConfig()
    {
        return $this->config;
    }
    
    public final function setConfig(Config $config)
    {
        if(isset($this->config))
        {
            throw new \UnsupportedOperationException("Config has already been set");
        }
        $this->config = $config;
    }
    
    public final function getApplicationContext()
    {
        return $this->applicatoinContext;
    }
    
    public final function setApplicationContext(ApplicationContext $context)
    {
        if(isset($this->applicatoinContext))
        {
            throw new \UnsupportedOperationException("Context has already been set");
        }
        $this->applicatoinContext = $context;
    }
    
    public static function get()
    {
        return $GLOBALS['application'];
    }
    
    public function getHomePage()
    {
        return $this->getConfig()->getHomePage();
    }
}

?>
