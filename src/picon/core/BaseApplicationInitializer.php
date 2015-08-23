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

namespace picon\core;

use picon\core\cache\CacheManager;
use picon\core\domain\config\Config;

/**
 * The base Application Initializer which loads only the config
 *
 * @author Martin Cassidy
 * @package core
 */
class BaseApplicationInitializer extends ApplicationInitializer
{
    /**
     * Load the application config
     * @return Config
     */
    protected function loadConfig()
    {
        $config = null;
        if(CacheManager::resourceExists(self::CONFIG_RESOURCE_NAME, CacheManager::APPLICATION_SCOPE))
        {
            $config = CacheManager::loadResource(self::CONFIG_RESOURCE_NAME, CacheManager::APPLICATION_SCOPE);
        }
        else
        {
            $config = ConfigLoader::load(CONFIG_FILE);
            CacheManager::saveResource(self::CONFIG_RESOURCE_NAME, $config, CacheManager::APPLICATION_SCOPE);
        }
        PiconApplication::get()->getConfigLoadListener()->onConfigLoaded($config);
        return $config;
    }
    
    public function initialise()
    {
        $this->loadConfig();
    }
}

?>
