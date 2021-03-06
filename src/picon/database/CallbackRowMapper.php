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
 * A row mapper that will run the provided callback for each record in a result set
 * 
 * The callback is expected to take a single argument for the row and return a mapped
 * object
 * 
 * @author Martin Cassidy
 * @package database
 */
class CallbackRowMapper implements RowMapper
{
    private $callback;
    
    public function __construct($callback)
    {
        Args::callBackArgs($callback, 1, 'callback');
        $this->callback = $callback;
    }
    
    public function mapRow($row)
    {
        $callable = $this->callback;
        return $callable($row);
    }
}

?>
