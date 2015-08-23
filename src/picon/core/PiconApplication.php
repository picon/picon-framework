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
 * 
 * $HeadURL$
 * $Revision$
 * $Author$
 * $Date$
 * $Id$
 * 
 */

namespace picon;

require_once(dirname(__FILE__)."/../core/ApplicationInitializer.php");

//Andendum must bypass the auto loader
require_once(dirname(__FILE__) . "/addendum/annotation_parser.php");
require_once(dirname(__FILE__) . "/addendum/annotations.php");
require_once(dirname(__FILE__) . "/addendum/doc_comment.php");

require_once(dirname(__FILE__) . "/../cache/CacheManager.php");
require_once(dirname(__FILE__) . "/../cache/PiconSerializer.php");

/**
 * This is the main class for a Picon Application.
 * It loads and stores the config and context.
 * 
 * Although the constructor is public, it is not expected that there will
 * be more than one instance of this class, a singleton by convention.
 * 
 * The application will always be available via the GLOBALS super global using
 * the <b>picon-application</b> key.
 * 
 * The application may also be reached through PiconApplication::get();
 * 
 * @author Martin Cassidy
 */
abstract class PiconApplication
{
    /**
     * The key to store tha application in within globals
     */

    const GLOBAL_APPLICATION_KEY = "picon-application";

    /**
     * Stores the application context
     */
    private $applicatoinContext;

    /**
     * Stores the configuration
     */
    private $config;

    /**
     * Stores a class which helps with application initialisation
     */
    private $initialiser;

    /**
     * An array of ApplicationInitializerConfigLoadListener
     * @var array 
     */
    private $configLoadListeners;

    /**
     * An array of ApplicationInitializerContextLoadListener
     * @var array 
     */
    private $contextLoadListeners;

    /**
     * An array of Converterss
     * @var array 
     */
    private $converters = array();

    /**
     * Create a new Picon Application
     * Fires off the application initialiser to load an instantiat all resources
     * Despite not being private, like a normal singleton, it is not
     * expected for a Picon Application to be instantiated more than once
     */
    public function __construct()
    {
        if (isset($GLOBALS[self::GLOBAL_APPLICATION_KEY]))
        {
            throw new \IllegalStateException("An instance of picon application already exists");
        }
        $GLOBALS[self::GLOBAL_APPLICATION_KEY] = $this;

        $this->initialiser = $this->getApplicationInitializer();

        //@todo sort this out once the namespaces are all correct and get this working with composer
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY, 'picon');
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/annotations");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/annotations");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/exceptions");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/pages");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web", "picon\\web");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/request", "picon\\web\\request");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/security", "picon\\web\\security");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/security/authorisation", "picon\\web\\security\\authorisation");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/context", "picon\\context");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/markup/html/form", "picon\\web\\markup\\html\\form");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/ajax/markup/html", "picon\\web\\ajax\\markup\\html");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY . "/web/ajax", "picon\\web\\ajax");
        $this->initialiser->addScannedDirectory(ASSETS_DIRECTORY);

        $this->internalInit();

        $this->initialiser->initialise();

        ob_start();
    }
    
    protected abstract function getApplicationInitializer();

    protected function internalInit()
    {
        $this->configLoadListeners = new ApplicationInitializerConfigLoadListenerCollection();
        $this->contextLoadListeners = new ApplicationInitializerContextLoadListenerCollection();

        $config = &$this->config;
        $context = &$this->applicatoinContext;

        $this->addConfigLoaderListener(new ApplicationConfigLoadListener(function($loadedConfig) use (&$config)
        {
            $config = $loadedConfig;
        }));
        $this->addContextLoaderListener(new ApplicationContextLoadListener(function($createdContext) use (&$context)
        {
            $context = $createdContext;
        }));
    }


    public abstract function run();

    public final function getConfig()
    {
        return $this->config;
    }

    public final function getApplicationContext()
    {
        return $this->applicatoinContext;
    }

    public final function getProfile()
    {
        return $this->config->getProfile();
    }

    public static function get()
    {
        if (!isset($GLOBALS[self::GLOBAL_APPLICATION_KEY]))
        {
            throw new \IllegalStateException("Failed to get picon application. The application has not been instantiated.");
        }
        return $GLOBALS[self::GLOBAL_APPLICATION_KEY];
    }

    public function getConfigLoadListener()
    {
        return $this->configLoadListeners;
    }

    public function getContextLoadListener()
    {
        return $this->contextLoadListeners;
    }

    public function addConfigLoaderListener(ApplicationInitializerConfigLoadListener $listener)
    {
        $this->configLoadListeners->add($listener);
    }

    public function addContextLoaderListener(ApplicationInitializerContextLoadListener $listener)
    {
        $this->contextLoadListeners->add($listener);
    }

    public function getConverter($className)
    {
        if (array_key_exists($className, $this->converters))
        {
            return $this->converters[$className];
        }
        return null;
    }
}

?>
