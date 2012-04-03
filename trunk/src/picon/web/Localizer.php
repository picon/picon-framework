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
 * Localizer for a specific component
 * It is through this method that internationalization support will
 * be added in the future
 * 
 * @author Martin Cassidy
 * @package web
 */
class Localizer
{
    const EXTENSION = '.properties';
    const PROPERTIES_CACHE_NAME = 'localizer_properties';
    
    private $component;
    private $properties;
    
    private function __construct(Component $component)
    {
        $this->component = $component;
        $page = $component->getPage()==null?"":get_class($component->getPage());
        $resourceName = self::PROPERTIES_CACHE_NAME.'_'.$page.'_'.$component->getComponentPath();
        if(CacheManager::resourceExists($resourceName, CacheManager::APPLICATION_SCOPE))
        {
            $this->properties = CacheManager::loadResource($resourceName, CacheManager::APPLICATION_SCOPE);
        }
        else
        {
            $this->properties = $this->getProperties($this->component);
            CacheManager::saveResource($resourceName, $this->properties, CacheManager::APPLICATION_SCOPE);
        }
    }
    
    public static function get(Component $component)
    {
        if($component->getParent()==null && !($component instanceof WebPage))
        {
            trigger_error('It is not safe to rely on the localizer until the component hierarchy is complete', E_USER_WARNING);
        }
        
        return new self($component);
    }
    
    private function getProperties(Component $component)
    {
        $target = $component;
        $properties = array();
        
        while($target!=null)
        {
            $reflection = new \ReflectionClass($target);
            
            while($reflection!=null)
            {
                $fileInfo = new \SplFileInfo($reflection->getFileName());
                $fileName = $fileInfo->getPath()."\\".$reflection->getShortName().self::EXTENSION;
                if(file_exists($fileName))
                {
                    $loaded = $this->loadProperties($fileName);
                    foreach($loaded as $name => $value)
                    {
                        if(!array_key_exists($name, $properties))
                        {
                            $properties[$name] = $value;
                        }
                    }
                }
                $reflection = $reflection->getParentClass();
            }
            $target = $target->getParent();
        }
        return $properties;
    }
    
    private function loadProperties($fileName)
    {
        $properties = array();
        $fileHandle = fopen($fileName, 'r');
        while (!feof($fileHandle))
        {
            $raw = fgets($fileHandle, 4096);
            $pair = explode('=', $raw);
            if(count($pair)==2)
            {
                $name = trim($pair[0]);
                $value = trim($pair[1]);
                $properties[$name] = $value;
            }
        }
        fclose($fileHandle);
        return $properties;
    }
    
    /**
     * Find a key in the localized properties for the component
     * If an exact match cannot be located a less acurate one will be used
     * for example if Page.Form.Required does not exists Form.Required will be 
     * used instead.
     * @param string $key The key to search for
     */
    public function getString($key, $model = null)
    {
        $key = str_replace(Component::PATH_SEPERATOR, '.', $this->component->getComponentPath()).$key;
        $string = null;
        if($model!=null && !($model instanceof Model))
        {
            throw new \InvalidArgumentException(sprintf("Localizer::getString() expects argument 2 to be a Model"));
        }
        $string = $this->internalGetString($key);
        
        if($string!=null && $model!=null)
        {
            $string = $this->interpolate($string, $model);
        }
        
        return $string;
    }
    
    private function internalGetString($key)
    {
        if(array_key_exists($key, $this->properties))
        {
            return $this->properties[$key];
        }
        else
        {
            $keyHierarchy = explode('.', $key);

            if(count($keyHierarchy)>1)
            {
                unset($keyHierarchy[0]);
                $lesserKey = implode('.', $keyHierarchy);
                return $this->internalGetString($lesserKey);
            }
        }
    }
    
    
    /**
     * @todo extract this out to a helper
     * @param type $string
     * @param Model $model 
     */
    private function interpolate($string, Model $model)
    {
        $object = $model->getModelObject();
        
        if(is_object($object))
        {
            $reflection = new \ReflectionClass($object);
            foreach($reflection->getProperties() as $property)
            {
                $property->setAccessible(true);
                $name = $property->getName();
                $value = $property->getValue($object);
                
                if(is_array($value))
                {
                    $string = $this->interpolate($string, new ArrayModel($value));
                }
                else if(is_object($value))
                {
                    throw new \UnsupportedOperationException('Recursive interpolation not supported');
                }
                else
                {
                    $string = str_replace("\${".$name."}", $value, $string);
                }
            }
        }
        else if(is_array($object))
        {
            foreach($object as $key => $value)
            {
                $string = str_replace("\${".$key."}", $value, $string);
            }
        }
        return $string;
    }
}

?>
