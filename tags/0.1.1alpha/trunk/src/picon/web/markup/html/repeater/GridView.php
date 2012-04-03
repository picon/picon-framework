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
 * Literally a repeating view of repeating views. Allows for work with both
 * rows and coloumns, such as a table
 * 
 * @author Martin Cassidy
 * @package web/markup/html/repeater
 */
class GridView extends RepeatingView
{
    private $columns;
    private $rows = array();
    private $columnId;
    private $callback;
    
    public function __construct($id, $columnId, $columns, $callback = null, $model = null)
    {
        parent::__construct($id, $model);
        
        if($callback!=null)
        {
            Args::callBackArgs($callback, 1, 'callback');
        }
        $this->columns = $columns;
        $this->columnId = $columnId;
        $this->callback = $callback;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    protected function populate()
    {
        $this->removeAll();
        
        foreach($this->getRecords() as $index => $object)
        {
            $this->populateRow(new BasicModel($object), $index);
        }
    }
    
    protected function populateRow(Model $model, $index)
    {
        $row = new ListItem($this->getNextChildId(), $model, $index);
        array_push($this->rows, $row);
        $this->add($row);
        $cells = new RepeatingView($this->columnId);
        $row->add($cells);
        for($i = 0; $i < $this->getColumns(); $i++)
        {
            $item = new GridItem($cells->getNextChildId(), $model, $index, $i);
            $cells->add($item);
            $this->populateItem($item);
        }
    }
    
    protected function removeAll()
    {
        foreach($this->rows as $row)
        {
            $this->remove($row);
        }
        $this->rows = array();
    }
    
    protected function populateItem(GridItem $item)
    {
        if($this->callback==null)
        {
            throw new \IllegalStateException('When not passing a callback to GridView it is expected that the populateItem() method will be overriden');
        }
        $callablel = $this->callback;
        $callablel($item);
    }
    
    protected function getRecords()
    {
        return $this->getModelObject();
    }
}

?>
