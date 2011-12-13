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
 * Description of DataBaseTemplate
 * 
 * @author Martin Cassidy
 */
class DataBaseTemplate implements DataBaseOperations
{
    private $source;
    private $driver;
    
    public function __construct(DataSource $source)
    {
        $this->source = $source;
        $this->driver = $source->getDriver();
    }
    
    public function getConnection()
    {
        return $this->source->getConnection();
    }
    
    public function getDataSource()
    {
        return $this->source;
    }
    
    public function __destruct()
    {
        $this->driver->dissconnect($this->getConnection());
    }
    
    public function execute($sql)
    {
        $this->driver->query($sql, $this->getConnection());
    }
    
    public function query($sql, RowMapper $mapper, $arguments = null)
    {
        $records = array();
        $sql = $this->prepareArgs($sql, $arguments);
        $results = $this->driver->query($sql, $this->getConnection());
        
        while($row = $this->driver->resultSetObject($results))
        {
            $record = $mapper->mapRow($row);
            Args::notNull($record, 'row mapper return value');
            array_push($records, $record);
        }
        return $records;
    }
    
    public function update($sql, $arguments = null)
    {
        $sql = $this->prepareArgs($sql, $arguments);
        $this->driver->query($sql, $this->getConnection());
        return $this->driver->getAffectedRows($this->getConnection());
    }
    
    private function prepareArgs($sql, $arguments = null)
    {
        if($arguments!=null)
        {
            eval("\$sql = sprintf(\$sql, ". implode(',', $arguments).")");
        }
        return $sql;
    }
}

?>
