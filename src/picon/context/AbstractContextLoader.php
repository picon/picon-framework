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

namespace picon\context;

use Annotation;
use picon\core\ApplicationInitializer;
use picon\core\cache\CacheManager;
use picon\core\config\Config;

/**
 * Context loader super class
 * 
 * @author Martin Cassidy
 * @package context
 */
abstract class AbstractContextLoader
{
    const CONTEXT_RESOURCE_NAME = 'application_context';
    
    private $resourceMap;
    private $resources = array();
    
    public function load(Config $config)
    {
        $this->resourceMap = CacheManager::loadResource(self::CONTEXT_RESOURCE_NAME, CacheManager::APPLICATION_SCOPE);
        
        if($this->resourceMap==null)
        {
            ApplicationInitializer::loadAssets(ASSETS_DIRECTORY);
            $this->resourceMap = $this->loadResourceMap($this->getClasses());
            CacheManager::saveResource(self::CONTEXT_RESOURCE_NAME, $this->resourceMap, CacheManager::APPLICATION_SCOPE);
        }

        $this->createResources();
        
        $this->loadDataSources($config->getDataSources());
        return new ApplicationContext($this->resources);
    }
    
    protected abstract function loadResourceMap($classes);
    
    protected abstract function loadDataSources($sourceConfig);
    
    private function createResources()
    {
        foreach($this->resourceMap as $name => $class)
        {
            $this->pushToResourceMap($name, new $class());
        }
    }
    
    /**
     * Adds the given resource to the map of resources
     * @param String $resourceName The name of the resource
     * @param Object $resource The research
     */
    protected function pushToResourceMap($resourceName, $resource)
    {
        if(array_key_exists($resourceName, $this->resources))
        {
            throw new \picon\core\exceptions\DuplicateResourceException(sprintf("The resource %s already exists.", $resourceName));
        }
        $this->resources[$resourceName] = $resource;
    }
    
    /**
     * Gets the resource name for an object. By default this is the class
     * name (with a lowercase first letter e.g. class MyResource is named
     * myResource) If name has been specified in the annotation then
     * the name is extracted from there instead.
     * @param Annotation $annotation
     * @param String $className
     * @return string
     */
    protected function getResourceName(Annotation $annotation, $className)
    {
        $name = $annotation->name;
        
        if($name=="")
        {
            return strtolower(substr($className, 0, 1)).substr($className,1,strlen($className));
        }
        else
        {
            return $name;
        }
    }
    
    public static function getClasses()
    {
        return get_declared_classes();
    }
    
    public function createDataSources()
    {
        
    }
}

?>
