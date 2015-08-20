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
 */

namespace picon\web;

use \picon\PiconApplication;
use \picon\ApplicationContextLoadListener;
use \picon\web\security\WebApplicationSecuritySettings;

require_once(dirname(__FILE__)."/../core/PiconApplication.php");
require_once(dirname(__FILE__)."/application/WebApplicationInitializer.php");


/**
 * A Picon Application for producing web pages
 *
 * @author Martin Cassidy
 */
class PiconWebApplication extends PiconApplication
{
    private $requestProcessor;
    
    //Component listeners
    private $componentInstantiationListeners;
    private $componentInitializationListeners;
    private $componentBeforeRenderListeners;
    private $componentAfterRenderListeners;
    private $componentRenderHeadListener;
    
    private $pageMapInitializationListener;
	
    private $securitySettings;
    
    protected function getApplicationInitializer()
    {
        return new WebApplicationInitializer();
    }
    
    protected final function internalInit()
    {
        parent::internalInit();
        $this->addContextLoaderListener(new ApplicationContextLoadListener(function($createdContext)
        {
            session_start();
        }));
        
        $this->securitySettings = new WebApplicationSecuritySettings();
        
        $this->pageMapInitializationListener = new PageMapInitializationListenerCollection();

        $this->componentInstantiationListeners = new ComponentInstantiationListenerCollection();
        $this->addComponentInstantiationListener(new ComponentInjector());
        $this->addComponentInstantiationListener(new ComponentAuthorisationListener());

        $this->componentInitializationListeners = new ComponentInitializationListenerCollection();
        $this->componentBeforeRenderListeners = new ComponentBeforeRenderListenerCollection();
        $this->componentAfterRenderListeners = new ComponentAfterRenderListenerCollection();
        $this->componentRenderHeadListener = new ComponentRenderHeadListenerCollection();

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
    
    public function getHomePage()
    {
        return $this->getConfig()->getHomePage();
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
    
    public function getComponentRenderHeadListener()
    {
        return $this->componentRenderHeadListener;
    }
    
    public function getPageMapInitializationListener()
    {
        return $this->pageMapInitializationListener;
    }
    
    public function addComponentInstantiationListener(ComponentInstantiationListener $listener)
    {
        $this->componentInstantiationListeners->add($listener);
    }
    
    public function addComponentInitializationListener(ComponentInitializationListener $listener)
    {
        $this->componentInitializationListener->add($listener);
    }
    
    public function addComponentBeforeRenderListener(ComponentBeforeRenderListener $listener)
    {
        $this->componentBeforeRenderListeners->add($listener);
    }
    
    public function addComponentAfterRenderListenersr(ComponentAfterRenderListener $listener)
    {
        $this->componentAfterRenderListeners->add($listener);
    }
    
    public function addComponentRenderHeadListener(ComponentRenderHeadListener $listener)
    {
        $this->componentRenderHeadListener->add($listener);
    }
    
    public function addPageMapInitializationListenerCollection(PageMapInitializationListenerCollection $listener)
    {
        $this->pageMapInitializationListener->add($listener);
    }
    
    public function getSecuritySettings()
    {
        return $this->securitySettings;
    }
    
    public function __destruct()
    {
        ob_end_flush();
    }
}

?>
