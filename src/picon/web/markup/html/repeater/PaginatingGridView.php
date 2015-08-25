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

namespace picon\web\markup\html\repeater;

use picon\core\Args;
use picon\web\markup\html\paging\Pageable;

/**
 * A grid view which is paginatable
 * 
 * @author Martin Cassidy
 * @package web/markup/html/repeater
 */
class PaginatingGridView extends GridView implements Pageable
{
    private $rowsPerPage;
    private $currentPage = 1;
    
    public function __construct($id, $columnId, $columns, $rowsPerPage, $callback = null, $model = null)
    {
        parent::__construct($id, $columnId, $columns, $callback, $model);
        Args::isNumeric($rowsPerPage, 'rowsPerPage');
        $this->rowsPerPage = $rowsPerPage;
    }
    
    public function setRowsPerPage($rowsPerPage)
    {
        Args::isNumeric($rows, 'rowsPerPage');
        $this->rowsPerPage = $rowsPerPage;
    }
    
    public function getRowsPerPage()
    {
        return $this->rowsPerPage;
    }
    
    public function getPageCount()
    {
        return ceil(count($this->getModelObject())/$this->rowsPerPage);
    }
    
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
    
    public function setCurrentPage($page)
    {
        Args::isNumeric($page, 'page');
        $this->currentPage = $page;
    }
    
    protected function getRecords()
    {
        return array_slice($this->getModelObject(), ($this->currentPage-1)*$this->rowsPerPage, $this->rowsPerPage);
    }
    
    public function isStateless()
    {
        return false;
    }
}

?>
