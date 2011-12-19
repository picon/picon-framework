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
 * Description of MarkupLoader
 * 
 * @author Martin Cassidy
 */
class MarkupLoader
{
    const MARKUP_RESOURCE_PREFIX = 'markup_';
    
    private static $instance;
    private static $extensions = array('html', 'htm');
    
    private function __construct()
    {
        
    }
    
    public static function get()
    {
        if(!isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function loadMarkup(Component $component)
    {
        $name = get_class($component);
        $fileSafeName = str_replace('\\', '_', $name);
        if(CacheManager::resourceExists(self::MARKUP_RESOURCE_PREFIX.$fileSafeName, CacheManager::APPLICATION_SCOPE))
        {
            return CacheManager::loadResource(self::MARKUP_RESOURCE_PREFIX.$fileSafeName, CacheManager::APPLICATION_SCOPE);
        }
        $markup = $this->internalLoadMarkup($name);
        CacheManager::saveResource(self::MARKUP_RESOURCE_PREFIX.$fileSafeName, $markup, CacheManager::APPLICATION_SCOPE);
        return $markup;
    }
    
    private function internalLoadMarkup($className)
    {
        $reflection = new \ReflectionClass($className);
        $fileInfo = new \SplFileInfo($reflection->getFileName());
        $parser = new MarkupParser();
        
        foreach(self::$extensions as $extension)
        {
            $file = $fileInfo->getPath()."\\".$reflection->getShortName().'.'.$extension;
            if(file_exists($file))
            {
                return $this->completeMarkup($parser->parse($file), $className);
            }
        }
        if(get_parent_class($className)!=false)
        {
            return $this->internalLoadMarkup(get_parent_class($className));
        }
        return null;
    }
    
    private function completeMarkup($markup, $className)
    {
        $extension = MarkupUtils::findPiconTag('extend', $markup);
        if($extension!=null)
        {
            $parentMarkup = $this->internalLoadMarkup(get_parent_class($className));
            if($parentMarkup==null)
            {
                throw new \MarkupNotFoundException(sprintf("Found picon:extend in markup for %s but there is no parent markup", $className));
            }
            $child = MarkupUtils::findPiconTag('child', $parentMarkup);
            if($child==null)
            {
                throw new \MarkupNotFoundException(sprintf("Component %s has inherited markup from %s but the inherited markup does not contain a picon:child tag", $className, get_parent_class($className)));
            }
            $child->addChild($extension);      
            return $parentMarkup;
        }
        return $markup;
    }
}

?>
