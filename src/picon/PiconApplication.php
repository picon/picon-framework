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
require_once("core/ApplicationInitializer.php");

require_once("addendum/annotation_parser.php");
require_once("addendum/annotations.php");
require_once("addendum/doc_comment.php");

/**
 * This is the main class for the entire application.
 * @author Martin Cassidy
 */
abstract class PiconApplication 
{
    private $applicatoinContext;
    private $config;
    private $requestProcessor;
    private $initialiser;
    
    //Application Initializer Listeners
    private $configLoadListeners;
    private $contextLoadListeners;
    
    //Component listeners
    private $componentInstantiationListeners;
    private $componentInitializationListeners;
    private $componentBeforeRenderListeners;
    private $componentAfterRenderListeners;
    
    //Converter
    private $converters = array();
    
    private $securitySettings;
    
    /**
     * Create a new Picon Application
     * Fires off the application initialiser to load an instantiat all resources
     * Despite not being private, like a normal singleton, it is not
     * expected for a Picon Application to be instantiated more than once
     */
    public function __construct()
    {
        ob_start();
        if(isset($GLOBALS['application']))
        {
            throw new \IllegalStateException("An instance of picon application already exists");
        }
        $GLOBALS['application'] = $this;
        
        $this->initialiser = new ApplicationInitializer();
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY, 'picon');
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY."\\annotations");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY."\\web\\annotations");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY."\\exceptions");
        $this->initialiser->addScannedDirectory(PICON_DIRECTORY."\\web\\pages");
        $this->initialiser->addScannedDirectory(ASSETS_DIRECTORY);
        
        $this->internalInit();
        
        $this->initialiser->initialise();
    }
    
    private function internalInit()
    {
        $this->securitySettings = new WebApplicationSecuritySettings();
        
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
            session_start();
        }));
        
        $this->componentInstantiationListeners = new ComponentInstantiationListenerCollection();
        $this->addComponentInstantiationListener(new ComponentInjector());
        $this->addComponentInstantiationListener(new ComponentAuthorisationListener());
        
        $this->componentInitializationListeners = new ComponentInitializationListenerCollection();
        $this->componentBeforeRenderListeners = new ComponentBeforeRenderListenerCollection();
        $this->componentAfterRenderListeners = new ComponentAfterRenderListenerCollection();
        
        $this->init();
    }
    
    /**
     * Called once the application has been created but not run the application initializer
     * This method creates listener collections.
     */
    public function init()
    {

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
    
    public final function getApplicationContext()
    {
        return $this->applicatoinContext;
    }
    
    public static function get()
    {
        return $GLOBALS['application'];
    }
    
    public function getHomePage()
    {
        return $this->getConfig()->getHomePage();
    }
    
    public function getConfigLoadListener()
    {
        return $this->configLoadListeners;
    }
    
    public function getContextLoadListener()
    {
        return $this->contextLoadListeners;
    }
    
    public function getComponentInstantiationListener()
    {
        return $this->componentInstantiationListeners;
    }
    
    public function getComponentInitializationListener()
    {
        return $this->componentInitializationListeners;
    }
    
    public function getComponentBeforeRenderListener()
    {
        return $this->componentBeforeRenderListeners;
    }
    
    public function getComponentAfterRenderListenersr()
    {
        return $this->componentAfterRenderListeners;
    }
    
    public function addConfigLoaderListener(ApplicationInitializerConfigLoadListener $listener)
    {
        $this->configLoadListeners->add($listener);
    }
    
    public function addContextLoaderListener(ApplicationInitializerContextLoadListener $listener)
    {
        $this->contextLoadListeners->add($listener);
    }
    
    public function addComponentInstantiationListener(ComponentInstantiationListener $listener)
    {
        $this->componentInstantiationListeners->add($listener);
    }
    
    public function addComponentInitializationListener(ComponentInitializationListener $listener)
    {
        return $this->componentInitializationListener->add($listener);
    }
    
    public function addComponentBeforeRenderListener(ComponentBeforeRenderListener $listener)
    {
        return $this->componentBeforeRenderListeners->add($listener);
    }
    
    public function addComponentAfterRenderListenersr(ComponentAfterRenderListener $listener)
    {
        return $this->componentAfterRenderListeners->add($listener);
    }
    
    public function getConverter($className)
    {
        if(array_key_exists($className, $this->converters))
        {
            return $this->converters[$className];
        }
        return null;
    }
    
    public function __destruct()
    {
        ob_end_flush();
    }
    
    public function getSecuritySettings()
    {
        return $this->securitySettings;
    }
}

?>
