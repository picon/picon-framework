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

namespace picon\web;

use picon\core\cache\CacheManager;
use picon\core\utils\MarkupParser;
use picon\core\utils\MarkupUtils;
use picon\core\PiconApplication;

/**
 * The mark-up loads finds, loads and parses the mark-up for a component class.
 * This also performs the mark-up merge for picon:child and picon:extend
 * 
 * @todo the head processing and markup inheritnce is a bit of message and will
 * fall over if the mark-up is not written properly. Need to add some sanity checks
 * @author Martin Cassidy
 * @package web/markup
 */
class MarkupLoader
{
    /**
     * Constant for the markup resource cache
     */
    const MARKUP_RESOURCE_PREFIX = 'markup_';
    
    /**
     * Self instance
     * @var MarkupLoader
     */
    private static $instance;
    
    /**
     * The supported extensions for mark-up
     * @var array
     */
    private static $extensions = array('html', 'htm');
    
    /**
     * Singleton
     */
    private function __construct()
    {
        
    }
    
    /**
     * Get the markup loader
     * @return MarkupLoader 
     */
    public static function get()
    {
        if(!isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load the associated markup file for a component
     * @param Component $component
     * @return MarkupElement The root markup tag, populated with child tags 
     */
    public function loadMarkup(Component $component)
    {
        $name = get_class($component);
        $fileSafeName = str_replace('\\', '_', $name);
        
        if(PiconApplication::get()->getProfile()->isCacheMarkup())
        {
            if(CacheManager::resourceExists(self::MARKUP_RESOURCE_PREFIX.$fileSafeName, CacheManager::APPLICATION_SCOPE))
            {
                return CacheManager::loadResource(self::MARKUP_RESOURCE_PREFIX.$fileSafeName, CacheManager::APPLICATION_SCOPE);
            }
            else
            {
                $markup = $this->internalLoadMarkup($name);
                CacheManager::saveResource(self::MARKUP_RESOURCE_PREFIX.$fileSafeName, $markup, CacheManager::APPLICATION_SCOPE);
                return $markup;
            }
        }
        else
        {
            return $this->internalLoadMarkup($name);;
        }
    }
    
    private function internalLoadMarkup($className)
    {
        $reflection = new \ReflectionClass($className);
        $fileInfo = new \SplFileInfo($reflection->getFileName());
        $parser = new MarkupParser();
        
        foreach(self::$extensions as $extension)
        {
            $file = $fileInfo->getPath()."/".$reflection->getShortName().'.'.$extension;
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
        //Auto add a head tag if not present
        $head = $markup->getChildByName('head');
        
        if($head==null)
        {
            $html = $markup->getChildByName('html');
            $body = $markup->getChildByName('body');
            $head = new PiconTag('head');
            $html->setChildren(array($head, $body));
        }
        
        $extension = MarkupUtils::findPiconTag('extend', $markup);
        if($extension!=null)
        {
            $parentMarkup = $this->internalLoadMarkup(get_parent_class($className));
            if($parentMarkup==null)
            {
                throw new \picon\core\exceptions\MarkupNotFoundException(sprintf("Found picon:extend in markup for %s but there is no parent markup", $className));
            }
            
            $child = $this->getChildTag($parentMarkup);
            if($child==null)
            {
                throw new \picon\core\exceptions\MarkupNotFoundException(sprintf("Component %s has inherited markup from %s but the inherited markup does not contain a picon:child tag", $className, get_parent_class($className)));
            }

            $childHead = $markup->getChildByName('picon:head');
            
            if($childHead!=null)
            {
                $head = $parentMarkup->getChildByName('head');
                $head->addChild($childHead);
            }
            
            $child->addChild($extension);
            return $parentMarkup;
        }
        
        return $markup;
    }
    
    private function getChildTag($markup)
    {
        $child = MarkupUtils::findPiconTag('child', $markup);
        
        if($child==null)
        {
            return null;
        }
        
        $existingExtension = MarkupUtils::findPiconTag('extend', $child);
        if($existingExtension==null)
        {
            return $child;
        }
        else
        {
            return $this->getChildTag($existingExtension);
        }
    }
}

?>
