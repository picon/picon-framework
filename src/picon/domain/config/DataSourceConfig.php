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
 * Description of DataSourceConfig
 *
 * @author Martin Cassidy
 */
class DataSourceConfig extends ComonDomainBase
{
    private $type;
    private $host;
    private $port;
    private $username;
    private $password;
    private $database;
    
    /**
     *
     * @param DataSourceType $type
     * @param String $host
     * @param int $port
     * @param String $username
     * @param String $password
     * @param String $database 
     */
    public function __construct(DataSourceType $type, $host, $port, $username, $password, $database)
    {
        $this->host = $host;
        $this->type = $type;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }
}

?>
