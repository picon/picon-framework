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

use mindplay\annotations\Annotations;
use picon\database\source\DataSourceFactory;

/**
 * Automatic loader for context resources
 * Loads resources bassed on pre defined annotations (picon\core\annotations\Service, Repository)
 * 
 * @author Martin Cassidy
 * @package context
 */
class AutoContextLoader extends AbstractContextLoader
{
    public function loadResourceMap($classes)
    {
       $resources = array();
       foreach($classes as $class)
       {
            $reflection = new \ReflectionClass($class);
            $name = "";

           $services = Annotations::ofClass($reflection, "@Service");
            if(count($services)==1)
            {
                $annotation = $services[0];
                $resources[$this->getResourceName($annotation, $class)] = $reflection->getName();
            }
           $repositories = Annotations::ofClass($reflection, "@Repository");
            if(count($repositories)==1)
            {
                $annotation = $repositories[0];
                $resources[$this->getResourceName($annotation, $class)] = $reflection->getName();
            }
        }
        return $resources;
    }
    
    protected function loadDataSources($sourceConfig)
    {
        foreach($sourceConfig as $config)
        {
            $source = DataSourceFactory::getDataSource($config);
            $this->pushToResourceMap($config->name, $source);
        }
    }
}

?>
