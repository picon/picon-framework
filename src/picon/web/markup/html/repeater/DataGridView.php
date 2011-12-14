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
 * A GridView which is generated from a data provider
 * 
 * @author Martin Cassidy
 */
class DataGridView extends PaginatingGridView
{
    private $dataProvider;
    private $columns;
    private $valueId;
    
    public function __construct($id, $columnId, $valueId, DataProvider $dataProvider, $columns, $rowsPerPage)
    {
        parent::__construct($id, $columnId, count($columns), $rowsPerPage);
        $this->dataProvider = $dataProvider;
        $this->columns = $columns;
        $this->valueId = $valueId;
    }
    
    protected function getRecords()
    {
        return $this->dataProvider->getRecords(($this->getCurrentPage()-1)*$this->getRowsPerPage(), $this->getRowsPerPage());
    }
    
    protected function populateItem(GridItem $item)
    {
        $column = $this->columns[$item->getColumnIndex()];
        $column->populateCell($item, $this->valueId, $item->getModel());
    }
    
    public function getPageCount()
    {
        return ceil($this->dataProvider->getSize()/$this->getRowsPerPage());
    }
}

?>
