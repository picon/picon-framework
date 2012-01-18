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
 * A model which runs from a property of an object
 * @todo this should use a property resolver helper to extract properties from object
 * @todo this should be able to support a model as the target
 * @todo this should be able to handle property name recursion e.g. person.name.first
 * @author Martin Cassidy
 * @package web/model
 */
class PropertyModel implements Model
{
    private $target;
    private $property;
    
    public function __construct(&$target, $property)
    {
        $this->target = $target;
        $this->property = $property;
    }
    
    public function getModelObject()
    {
        $name = $this->property;
        return $this->target->$name;
    }
    
    public function setModelObject(&$object)
    {
        $name = $this->property;
        $this->target->$name = $object;
    }
}

?>
