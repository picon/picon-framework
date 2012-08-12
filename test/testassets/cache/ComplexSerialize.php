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

/**
 * Description of ComplexSerialize
 * 
 * @author Martin Cassidy
 */
class ComplexSerialize extends ParentComplexObject
{
    private $closure;
    
    /**
     *
     * @Transient
     */
    private $transient = "defaultValue";
    
    /**
     *
     * @Service
     */
    private $service = "defaultValue";
    
    private $object;
    
    public function __construct()
    {
        $value1 = "1";
        $value2 = "2";
        $this->object = new SimpleSerialize();
        $this->closure = function() use($value1, $value2)
        {
            return "executing ".$value1.$value2;
        };
        $this->transient = "newValue";
        $this->service = "newValue2";
        $closure = $this->closure;
    }
    
    public function getClosure()
    {
        return $this->closure;
    }
    
    public function getTransient()
    {
        return $this->transient;
    }
    
    public function getService()
    {
        return $this->service;
    }
    
    public function getObject()
    {
        return $this->object;
    }
}

?>
