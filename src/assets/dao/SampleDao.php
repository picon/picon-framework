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

use picon\DaoSupport;
use picon\CallbackRowMapper;

/**
 * Description of SampleDao
 * 
 * @author Martin Cassidy
 * @Repository
 */
class SampleDao extends DaoSupport
{
    /**
     * @Resource(name = 'demo')
     */
    private $dataSource;
    
    protected function init()
    {
        $this->setDataSource($this->dataSource);
    }
    
    public function getTestData()
    {
        $maper = new CallbackRowMapper(function($row)
        {
            $data = new TestItem();
            $data->id = $row->id;
            $data->text = $row->sometest;
            $data->timestamp = $row->timestamp;
            return $data;
        });
        $results = $this->getTemplate()->query('SELECT * FROM testtable', $maper);
        print_r($results);
    }
}

?>
