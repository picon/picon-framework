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
 * Auto loads classes via the PHP autoload handle
 * 
 * @author Martin Cassidy
 * @package core
 */
class AutoLoader
{
    const AUTO_LOAD_RESOURCE_NAME = 'auto_loader';
    
    private $scannedDirectories = array();
    private $cachedPaths = null;
    
    public function __construct()
    {
        $this->cachedPaths = CacheManager::loadResource(self::AUTO_LOAD_RESOURCE_NAME, CacheManager::APPLICATION_SCOPE);
        
        if($this->cachedPaths==null)
        {
            $this->cachedPaths = array();
        }
        
        spl_autoload_register(array($this, "autoLoad"));
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
     * Internal method for loading classes, used by spl_autoload_register 
     * 
     * This method will trigger php error on failure instead of throwing exceptions
     * as exceptions are caught internally by the php auto loader
     * 
     * @param String $className the name of the class to load, including the
     * namespace e.g. picon\RequestProcessor
     */
    protected function autoLoad($className)
    {
        if(array_key_exists($className, $this->cachedPaths))
        {
            require_once($this->cachedPaths[$className]);
            return;
        }
        
        $success = false;
        $path = explode("\\", $className);
        
        $class = $path[count($path)-1];
        unset($path[count($path)-1]);
        $namespace = implode("\\", $path);
        
        if(empty($namespace))
        {
            $namespace = "default";
        }
        
        if(substr($namespace, 0, 1)=='\\')
        {
            $namespace = substr($namespace, 1, strlen($namespace)-1);
        }
        
        if(!array_key_exists($namespace, $this->scannedDirectories))
        {
            $this->onFail($namespace, $class);
        }
        
        foreach($this->scannedDirectories[$namespace] as $dir)
        {
            $success = $this->loadClass($dir, $class, $className);
            if($success)
            {
                break;
            }
        }
        if(!$success)
        {
            $this->onFail($namespace, $class);
        }
    }
    
    /**
     * Called when the auto loader fails to load the requested class
     * @param type $namespace
     * @param type $class 
     */
    protected function onFail($namespace, $class)
    {
        trigger_error(sprintf("Unable to load class %s in namespace %s, please check that the name of file matches tha name of the class and that the file is in a directory that is used by the class scanner", $class, $namespace),E_USER_ERROR);
    }
    
    /**
     * Load a class from a directory, this is recursive and will also search
     * sub directories. 
     * @param string $directory The root directory for the class
     * @param string $className The simple name of the class
     * @param string $fullName The fully qualified name of the class
     * @return boolean true if the class file was found, false if not 
     */
    private function loadClass($directory, $className, $fullName)
    {
        $d = dir($directory);
        $success = false;
        while ((false !== ($entry = $d->read()))&&!$success)
        {
            if($entry==$className.'.php')
            {
                require_once($directory."\\".$entry);
                $this->cachedPaths[$fullName] = $directory."\\".$entry;
                $success = true;
            }
            if(is_dir($directory."\\".$entry) && !preg_match("/^.{1}.?$/", $entry))
            {
                $success = $this->loadClass($directory."\\".$entry,$className, $fullName);
            }
        }
        $d->close();
        return $success;
    }
    
    public function __destruct()
    {
        CacheManager::saveResource(self::AUTO_LOAD_RESOURCE_NAME, $this->cachedPaths, CacheManager::APPLICATION_SCOPE);
    }
}

?>
