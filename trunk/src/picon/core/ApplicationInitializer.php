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
require_once("AutoLoader.php");
require_once('PiconErrorHandler.php');

/**
 * ApplicationInitialiser works like a bootstrap. This is the first step
 * in the Picon application and performs the following:
 * 
 * <ul><li>Registers handlers for auto auto loading classes and error handling</li>
 * <li>Loads, validates and pareses the xml configuration into a config object</li>
 * <li>Loads the page map</li>
 * <li>Instantiates and injects resources</li></ul>
 * 
 * @author Martin Cassidy
 * @package core
 */
class ApplicationInitializer
{
    const CONFIG_RESOURCE_NAME = 'picon_config';
    
    private $autoLoader;
    private $errorHandler;
    
    private static $dirs = array();
    
    public function __construct()
    {
        $this->autoLoader = new AutoLoader();
        $this->errorHandler = new PiconErrorHandler;
    }
    
    /**
     * Add a directory to the class auto load scanner for a particular namespace
     * @param String $directory The directory to scan
     * @param String $namespace The namespace (optional, if your class is in
     * the default namespace leave this blank)
     */
    public function addScannedDirectory($directory, $namespace = 'default')
    {
        $this->autoLoader->addScannedDirectory($directory, $namespace);
    }
    
    /**
     * Initialise the application
     */
    public function initialise()
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

        PageMap::get()->initialise();
    }
    
    /**
     * Runs a require_once() on all the php files in the given directory
     * and invokes itself on any sub directories
     * @param String $directory the working directory
     */
    public static function loadAssets($directory)
    {
        if(in_array($directory, self::$dirs))
        {
            return;
        }
        $d = dir($directory);
        while (false !== ($entry = $d->read()))
        {
            if(preg_match("/\s*.php{1}$/", $entry))
            {
                require_once($directory."/".$entry);
            }
            if(is_dir($directory."/".$entry) && !preg_match("/^.{1}.?$/", $entry))
            {
               self::loadAssets($directory."/".$entry);
            }
        }
        $d->close();
        array_push(self::$dirs, $directory);
    }

}

?>
