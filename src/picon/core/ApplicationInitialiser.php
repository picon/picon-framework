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
 */
class ApplicationInitialiser
{
    private $scannedDirectories = array();
    
    public function __construct()
    {
        spl_autoload_register("self::autoLoad");
        //@todo register error handlers
    }
    
    /**
     * Add a directory to the class auto load scanner for a particular namespace
     * @param String $directory The directory to scan
     * @param String $namespace The namespace (optional, if your class is in
     * the default namespace leave this blank)
     */
    public function addScannedDirectory($directory, $namespace = 'default')
    {
        if(!array_key_exists($namespace, $this->scannedDirectories))
        {
            $this->scannedDirectories[$namespace] = array();
        }
        array_push($this->scannedDirectories[$namespace], $directory);
    }
    
    /**
     * Initialise the application
     */
    public function initialise(PiconApplication $application)
    {
        $config = ConfigLoader::load(CONFIG_FILE);
        $application->setConfig($config);
        
        /*
         * @todo this is testing only, remove it
         */
        print_r(PageMapHolder::getPageMap());
        
        $loader = ContextLoaderFactory::getLoader($config);
        $context = $loader->load();
        $injector = new Injector($context);
        
        foreach($context->getResources() as $resource)
        {
            $injector->inject($resource);
        }
    }

    /**
     * Internal method for loading classes, used by spl_autoload_register 
     * 
     * This method will trigger php error on failure instead of throwing exceptions
     * as exceptions are caught internally by the php auto loader
     * 
     * @param String $className the name of the class to load, including the
     * namespace e.g. picon\RequestProcessor
     */
    private function autoLoad($className)
    {
        $success = false;
        $path = explode("\\", $className);
        
        $class = $path[count($path)-1];
        unset($path[count($path)-1]);
        $namespace = implode("\\", $path);
        
        if(empty($namespace))
        {
            $namespace = "default";
        }
        
        if(!array_key_exists($namespace, $this->scannedDirectories))
        {
            trigger_error(sprintf("Unable to load class %s from namespace %s. No directories for the namespace have been added", $class, $namespace),E_USER_ERROR);
        }
        
        foreach($this->scannedDirectories[$namespace] as $dir)
        {
            $success = $this->loadClass($dir, $class);
            if($success)
            {
                break;
            }
        }
        if(!$success)
        {
            trigger_error(sprintf("Unable to load class %s in namespace %s, please check that the name of file matches tha name of the class and that the file is in a directory that is used by the class scanner", $class, $namespace),E_USER_ERROR);
        }
    }
    
    /**
     * Load a class from a directory, this is recursive and will also search
     * sub directories. 
     * @param String $directory The root directory for the class
     * @param String $className The name of the class
     * @return Boolean true if the class file was found, false if not 
     */
    private function loadClass($directory, $className)
    {
        $d = dir($directory);
        $success = false;
        while ((false !== ($entry = $d->read()))&&!$success)
        {
            if($entry==$className.'.php')
            { 
                require_once($directory."\\".$entry);
                $success = true;
            }
            if(is_dir($directory."\\".$entry) && !preg_match("/^.{1}.?$/", $entry))
            {
                $success = $this->loadClass($directory."\\".$entry,$className);
            }
        }
        $d->close();
        return $success;
    }
}

?>
