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
    private $pages;
    private $pageId = 1;
    private $pageInstances;
    private $deserialize = array();
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
        if(isset($this->pages))
        {
            return;
        }
        $this->scanPages();
    }

    private function scanPages()
    {
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
        return self::get()->pageId;
    }
    
    /**
     * @todo validate id
     */
    public function getPageById($id)
    {
        $page = &$this->pageInstances[$id];
        if(!in_array($id, $this->deserialize))
        {
            array_push($this->deserialize, $id);
            PiconSerializer::unserialize($page);
        }
        return $page;
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
        if(!in_array($page->getId(), $this->deserialize))
        {
            array_push($this->deserialize, $page->getId());
        }
        $instances = &$this->pageInstances;
        $instances[$page->getId()] = $page;
    }
    
    public function __destruct()
    {
        foreach($this->deserialize as $pageid)
        {
             PiconSerializer::serialize($this->pageInstances[$pageid]);
        }
        $_SESSION['page_map'] = $this;
    }
    
    public function __wakeup()
    {
        $this->deserialize = array();
    }
}

?>
