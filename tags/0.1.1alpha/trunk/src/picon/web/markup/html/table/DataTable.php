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
 * A data table, showing information from a data provider, specified by
 * columns.
 * 
 * @todo add support for sorting and filtering
 * @author Martin Cassidy
 * @package web/markup/html/table
 */
class DataTable extends Panel implements Pageable
{
    private $provider;
    private $gridView;
    private $columns;
    private $head;
    private $foot;
    
    public function __construct($id, DataProvider $provider, $columns, $rowsPerPage = 10)
    {
        parent::__construct($id);
        $this->provider = $provider;
        $this->columns = $columns;
        $this->$rowsPerPage = $rowsPerPage;
        
        $this->gridView = new DataGridView('row', 'col', 'value', $provider, $columns, $rowsPerPage);
        $this->add($this->gridView);
        
        $this->head = new RepeatingView('head');
        $this->foot = new RepeatingView('foot');
        $this->add($this->head);
        $this->add($this->foot);
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->setName('div');
    }
    
    public function setRowsPerPage($rowsPerPage)
    {
        Args::isNumeric($rows, 'rowsPerPage');
        $this->gridView->setRowsPerPage($rowsPerPage);
    }
    
    public function getRowsPerPage()
    {
        return $this->gridView->getRowsPerPage();
    }
    
    public function addTopToolbar(AbstractToolbar $toolbar)
    {
        $this->head->add($toolbar);
    }
    
    public function addBottomToolbar(AbstractToolbar $toolbar)
    {
        $this->foot->add($toolbar);
    }
    
    public function getPageCount()
    {
        return $this->gridView->getPageCount();
    }
    
    public function setCurrentPage($page)
    {
        $this->gridView->setCurrentPage($page);
    }
    
    public function getCurrentPage()
    {
        return $this->gridView->getCurrentPage();
    }
    
    public function getNextToolbarId()
    {
        return $this->head->getNextChildId();
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
}

?>
