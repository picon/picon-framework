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
 * A repeating component which works from an array. A ListItem is added
 * for each one and a callback invoked to populate it.
 * 
 * @author Martin Cassidy
 * @package web/markup/html/repeater
 */
class ListView extends AbstractRepeater
{
    private $callback;
    private $items = array();
    
    /**
     *
     * @param String $id
     * @param closure $callback
     * @param Model $model optional
     */
    public function __construct($id, $callback, $model = null)
    {
        parent::__construct($id, $model);
        Args::callBackArgs($callback, 1, 'callback');
        $this->callback = $callback;
    }
    
    protected function getRenderArray()
    {
        return $this->items;
    }
    
    public function populateItem($entry)
    {
        $callable = $this->callback;
        $callable($entry);
    }
    
    protected function populate()
    {
        $this->items = array();
        foreach($this->getChildren() as $child)
        {
            $this->remove($child);
        }
        foreach($this->getModel()->getModelObject() as $index => $object)
        {
            $model = $this->getModel()->getModelObject();
            $entry = new ListItem($this->getId().$index, new BasicModel($model[$index]), $index);
            $this->populateItem($entry);
            $this->addOrReplace($entry);
            array_push($this->items, $entry);
        }
    }
}

?>
