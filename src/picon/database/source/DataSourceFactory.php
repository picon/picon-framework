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
 * Description of DataSourceFactory
 * 
 * @author Martin Cassidy
 */
class DataSourceFactory
{
    private static $drivers = array();
    
    private function __construct()
    {
        
    }
    
    public static function getDataSource(DataSourceConfig $config)
    {
        $driver = self::getDataBaseDriver($config->type);
        $connection = $driver->connect($config->host, $config->username, $config->password, $config->database, $config->port);
        return new DataSource($config, $connection, $driver);
    }
    
    private static function getDataBaseDriver(DataSourceType $type)
    {
        $driverName = $type->__toString();
        if(array_key_exists($driverName, self::$drivers))
        {
            return self::$drivers[$driverName];
        }
        $className = "\\picon\\".$driverName.'Driver';
        if(class_exists($className))
        {
            self::$drivers[$driverName] = new $className();
            return self::$drivers[$driverName];
        }
        throw new \InvalidArgumentException(sprintf('Database driver %s does not exist', $driverName));
    }
}

?>
