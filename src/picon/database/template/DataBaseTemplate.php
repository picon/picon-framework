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
 * Default implementation of DataBaseOperations
 * Provideds access to functionality through the source
 * 
 * @author Martin Cassidy
 * @package database/template
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
    
    public function insert($sql, $arguments = null)
    {
        $sql = $this->prepareArgs($sql, $arguments);
        $this->driver->query($sql, $this->getConnection());
        return $this->driver->getInsertedId($this->getConnection());
    }
    
    private function prepareArgs($sql, $arguments = null)
    {
        if($arguments!=null && count($arguments)>0)
        {
            $sprintfargs = array_merge(array($sql), $arguments);
            $sprintf = new \ReflectionFunction('sprintf');
            $sql = $sprintf->invokeArgs($sprintfargs);
        }
        return $sql;
    }
    
    public function queryForInt($sql, $arguments = null)
    {
        $sql = $this->prepareArgs($sql, $arguments);
        $int = null;
        $results = $this->driver->query($sql, $this->getConnection());
        
        if($this->driver->countRows($results)!=1 || $this->driver->countColumns($results)!=1)
        {
            throw new SQLException('Expected only 1 result with only 1 column');
        }
        while($row = $this->driver->resultSetArray($results))
        {
            $int = $row[0];
        }
        settype($int, Component::TYPE_INT);
        return $int;
    }
}

?>
