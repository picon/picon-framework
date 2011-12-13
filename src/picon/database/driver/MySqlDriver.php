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
 * Database driver for mysql
 * 
 * @author Martin Cassidy
 */
class MySqlDriver extends AbstractDatabaseDriver
{
    public function connect($host, $username, $password, $database, $port = null)
    {
        $connection = @mysql_connect($host, $username, $password);
        
        if($connection==false)
        {
            throw new SQLException(mysql_error());
        }
        $selection = @mysql_select_db($database, $connection);
        if($selection==false)
        {
            throw new SQLException(mysql_error());
        }
                
        return $connection;
    }
    
    public function dissconnect($connection)
    {
        mysql_close($connection);
    }
    
    public function getAffectedRows($connection)
    {
        return mysql_affected_rows($connection);
    }
    
    public function query($sql, $connection)
    {
        $result = @mysql_query($sql, $connection);
        if (!$result) 
        {
            throw new SQLException(mysql_error());
        }
        return $result;
    }
    
    public function resultSetObject($resultResource, $className = null)
    {
        if($className==null)
        {
            return mysql_fetch_object($resultResource);
        }
        return mysql_fetch_object($resultResource, $className);
    }
}

?>
