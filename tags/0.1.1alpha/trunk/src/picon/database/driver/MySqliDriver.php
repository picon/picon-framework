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
 * Database driver for mysqli
 * 
 * @author Martin Cassidy
 * @package database/driver
 */
class MySqliDriver extends AbstractDatabaseDriver
{
    public function connect($host, $username, $password, $database, $port = null)
    {
        $connection = new mysqli($host, $username, $password, $database, $port);
        
        if ($connection->connect_error) 
        {
            throw new SQLException($mysqli->connect_error,$mysqli->connect_errno);
        }
        return $connection;
    }
    
    public function dissconnect($connection)
    {
        $connection->close();
    }
    
    public function getAffectedRows($connection)
    {
        return $connection->affected_rows;
    }
    
    public function query($sql, $connection)
    {
        return $connection->query($sql);
    }
    
    public function resultSetObject($resultResource, $className = null)
    {
        if($className==null)
        {
            return $resultResource->fetch_object();
        }
        return $resultResource->fetch_object($className);
    }
    
    public function resultSetArray($resultResource)
    {
        return $resultResource->fetch_array();
    }
    
    public function countRows($resultResource)
    {
        return $resultResource->num_rows();
    }
    
    public function countColumns($resultResource)
    {
        return count($resultResource->fetch_fields());
    }
    
    public function getInsertedId($connection)
    {
        return $connection->insert_id;
    }
}

?>
