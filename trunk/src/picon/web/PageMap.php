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
 * Holder for the map to all statfull and statless web pages
 *
 * A singleton which will persist from one request to another via the session
 * For this reason it is important that all stored objects are serializable
 *
 * The page map stores a map of all stateless pages by both their direct path
 * (the name of the class, including its namespace) and also all specified
 * paths (pages which have been given @Path) Pagges are expected to extend 
 * WebPage @see WebPage
 * 
 * Additionally, actual instances of pages which have deemed statfull are stored
 * against an associated page id.
 *
 * @author Martin Cassidy
 * @package web
 */
class PageMap
{
    const PAGE_MAP_RESOURCE_NAME = 'pagemap';
    
    private $pages;
    private $pageId = 1;
    private $pageInstances = array();
    private static $self;

    /**
     * Private constructor, this is a singleton
     * Loads in ALL .php files from the assets directory
     * Builds the page map array
     */
    private function __construct()
    {
        //singleton
    }

    public function initialise()
    {
        if(isset($this->pages) && $this->pages!=null)
        {
            return;
        }
        $this->scanPages();
        CacheManager::saveResource(self::PAGE_MAP_RESOURCE_NAME, $this->pages, CacheManager::APPLICATION_SCOPE);
    }

    private function scanPages()
    {
        ApplicationInitializer::loadAssets(ASSETS_DIRECTORY);
        $this->pages = array();
        $scanner = new ClassScanner(array(new SubClassRule('\picon\WebPage')));

        $pages = $scanner->scanForName();
        foreach($pages as $pageName)
        {
            $this->addToPath($pageName, $pageName);
        }

        $pathScanner = new ClassScanner(array(new AnnotationRule('Path')));

        foreach($pathScanner->scanForReflection($pages) as $reflection)
        {
            $pathAnnoation = $reflection->getAnnotation('Path');
            $path = $pathAnnoation->path;

            if(empty($path))
            {
                throw new \UnexpectedValueException(sprintf("Expecting path annoation to have a path value for class %s", $reflection->getName()));
            }

            $this->addToPath($path, $reflection->getNamespaceName().'\\'.$reflection->getName());
        }
    }

    private function addToPath($path, $pageName)
    {
        if(array_key_exists($path, $this->pages))
        {
            throw new \DuplicatePageDefinitionException(sprintf("A page with path %s already exists and cannot be used again.", $path));
        }
        $this->pages[$path] = $pageName;
    }

    /**
     * Gets the array containing the page map
     * If this is the first invoke for this method, the page map will
     * be generated
     * @return Array The page map
     */
    public static function getPageMap()
    {
        return self::get()->pages;
    }
    
    public static function getNextPageId()
    {
        self::get()->pageId++;
        return 'page'.self::get()->pageId;
    }
    
    /**
     * 
     */
    public function getPageById($id)
    {
        if(array_key_exists($id, $this->pageInstances))
        {
            return $this->pageInstances[$id];
        }

        $page = CacheManager::loadResource($id, CacheManager::SESSION_SCOPE);
        
        if($page!=null)
        {
            $this->addOrUpdate($page);
            return $page;
        }
        else
        {
            return null;
        }
    }

    public static function get()
    {
        if (!isset(self::$self))
        {
            if (isset($_SESSION['page_map']))
            {
                self::$self = $_SESSION['page_map']; 
            }
            else
            {
                self::$self = new self();
            }
        }
        return self::$self;
    }
    
    public function addOrUpdate(WebPage &$page)
    {
        $instances = &$this->pageInstances;
        $instances[$page->getId()] = $page;
    }
    
    public function __sleep()
    {
        return array('pageId');
    }
    
    public function __wakeup()
    {
        $this->pages = CacheManager::loadResource(self::PAGE_MAP_RESOURCE_NAME, CacheManager::APPLICATION_SCOPE);
    }
    
    public function __destruct()
    {
        foreach($this->pageInstances as $pageid => $page)
        {
            CacheManager::saveResource($pageid, $page, CacheManager::SESSION_SCOPE);
        }
        $_SESSION['page_map'] = $this;
    }
}

?>