<?php
/**
 * Picon Framework
 * http://piconframework.com
 *
 * Copyright (C) 2011-2015 Martin Cassidy <martin.cassidy@webquub.com>

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

namespace picon\core;

use Doctrine\Common\Annotations\AnnotationRegistry;
use picon\core\cache\CacheManager;

/**
 * A ModuleInitialiser for the core module which registers core annotations, loads the source directories and core xml schema
 *
 * @package picon\core
 */
class CoreInitialiser implements ModuleInitialiser
{
    private $loaded = array();

    public function initialise()
    {
        $sources = Picon::$sources;

        AnnotationRegistry::registerAutoloadNamespace("picon\\core\\annotations", dirname(__FILE__)."/../..");

        if(!is_array($sources) || count($sources)<1)
        {
            throw new \InvalidArgumentException("Picon::$sources must be an array and must contain at least source location");
        }

        foreach($sources as $sources)
        {
            $this->loadSource($sources);
        }

        array_push(ConfigLoader::$configSchemas, dirname(__FILE__)."/config.xsd");
    }

    private function loadSource($sourceDirectory, &$required = null)
    {
        if(in_array($sourceDirectory, $this->loaded))
        {
            return;
        }

        $root = $required==null;
        if($required==null)
        {
            $required = array();
        }

        $sourceCacheName = sprintf("src_%s", str_replace("/", "_", stripslashes($sourceDirectory)));
        $sourceCache = CacheManager::loadResource($sourceCacheName, CacheManager::APPLICATION_SCOPE);

        if($sourceCache!=null)
        {
            $required = $sourceCache;
        }
        else
        {
            if(!is_dir($sourceDirectory))
            {
                throw new \InvalidArgumentException(sprintf("The directory %s could not be found or is not a directory", $sourceDirectory));
            }

            $directory = dir($sourceDirectory);

            while (false !== ($entry = $directory->read())) {
                if (preg_match("/\s*.php{1}$/", $entry)) {
                    array_push($required, $sourceDirectory . "/" . $entry);
                }
                if (is_dir($sourceDirectory . "/" . $entry) && !preg_match("/^.{1}.?$/", $entry)) {
                    $this->loadSource($sourceDirectory . "/" . $entry, $required);
                }
            }
            $directory->close();
        }
        array_push($this->loaded, $sourceDirectory);

        if($root)
        {
            foreach($required as $file)
            {
                require_once($file);
            }
            CacheManager::saveResource($sourceCacheName, $required, CacheManager::APPLICATION_SCOPE);
        }
    }
}