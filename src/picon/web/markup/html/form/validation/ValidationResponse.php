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
 * Description of ValidationResponse
 * 
 * @author Martin Cassidy
 */
class ValidationResponse
{
    private $key;
    private $name;
    private $value;
    private $values = array();
    
    public function __construct($key)
    {
        $this->key = $key;
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    public function addValue($key, $value)
    {
        $this->values[$key] = $value;
    }
    
    public function getValues()
    {
        return $this->values;
    }
    
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
}

?>
