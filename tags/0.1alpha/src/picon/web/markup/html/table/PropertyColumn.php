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
 * A column which shows a simple property of an object
 * 
 * @author Martin Cassidy
 * @package web/markup/html/table
 */
class PropertyColumn extends AbstractColumn
{
    private $propertyName;
    
    public function __construct($header, $propertyName)
    {
        parent::__construct($header);
        $this->propertyName = $propertyName;
    }
    
    /**
     * @todo this should get the property via a resolver helper
     * @param GridItem $item
     * @param type $componentId
     * @param Model $model 
     */
    public function populateCell(GridItem $item, $componentId, Model $model)
    {
        $property = $this->propertyName;
        $value = $model->getModelObject()->$property;
        $item->add(new Label($componentId, new BasicModel($value)));
    }
}

?>
