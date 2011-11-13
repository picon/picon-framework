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
 * Holds any and all resources instantiated by the application initialiser
 * @author Martin Cassidy
 * @package context
 */
class ApplicationContext
{
    private $resources = array();
    
    public function __construct($resources)
    {
        if(!is_array($resources))
        {
            throw new \InvalidArgumentException("Expected an array");
        }
        $this->resources = $resources;
    }
    
    public function getResources()
    {
        return $this->resources;
    }
    
    public function getResource($name)
    {
        if(array_key_exists($name, $this->resources))
        {
            return $this->resources[$name];
        }
        else
        {
            throw new \UndefinedResourceException(sprintf("The requested resource %s could not be found or the initialisation process is not complete", $name));
        }
    }
}

?>
