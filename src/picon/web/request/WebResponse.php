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

namespace picon\web\request;

/**
 * Basic implementation of a response
 *
 * @author Martin Cassidy
 * @package web/request
 */
class WebResponse implements Response
{
    private $body;
    private $headers = array();
    
    public function write($value)
    {
        $this->body .= $value;
    }
    
    public function flush()
    {
        foreach($this->headers as $header)
        {
            header($header['value'], true, $header['status']);
        }
        print($this->body);
    }
    
    public function clean()
    {
        $this->body = "";
        $this->headers = array();
    }
    
    public function getBody()
    {
        return $this->body;
    }
    
    public function setHeader($value, $status = null)
    {
        array_push($this->headers, array('value' => $value, 'status' => $status));
    }
}

?>
