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

namespace picon\context;

use picon\beans\InitializingBean;
use picon\core\BaseApplicationInitializer;
use picon\core\PiconApplication;

/**
 * An Application Initialiser which load context
 *
 * @author Martin Cassidy
 * @package context
 */
class ContextApplicationInitializer extends BaseApplicationInitializer
{
    /**
     * Load the application context
     * @return ApplicationContext
     */
    protected function loadContext($config)
    {
        $loader = ContextLoaderFactory::getLoader($config);
        $context = $loader->load($config);
        $injector = new Injector($context);
        
        foreach($context->getResources() as $resource)
        {
            $injector->inject($resource);
        }
        foreach($context->getResources() as $resource)
        {
            if($resource instanceof InitializingBean)
            {
                $resource->afterPropertiesSet();
            }
        }
        
        PiconApplication::get()->getContextLoadListener()->onContextLoaded($context);
    }
    
    public function initialise()
    {
        $config = $this->loadConfig();
        $this->loadContext($config);
    }

}

?>
