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
 * Basic implementation of request
 *
 * @author Martin Cassidy
 * @package web/request
 */
class WebRequest implements Request
{
    private $post;
    private $get;
    
    public function __construct()
    {
        $this->post = isset($_POST)?$_POST:false;
        $this->get = isset($_GET)?$_GET:false;
    }
    
    public function getQueryString()
    {
        return $_SERVER['QUERY_STRING'];
    }
    
    public function getPath()
    {
        return $_SERVER['REQUEST_URI'];
    }
    
    public function isAjax()
    {
        return isset($_GET['ajax']);
    }
    
    public function isResourceRequest()
    {
        return isset($_GET['picon-resource']);
    }
    
    public function getRootPath()
    {
        $root = preg_replace("/\/{1}\w*\.php$/", "", $_SERVER['PHP_SELF']);
        return $root;
    }
    
    public function isPost()
    {
        return $this->post!=false;
    }
    
    public function isGet()
    {
        return $this->get!=false;
    }
    
    /**
     * Get a POST parameter
     * @param type $name
     * @return type 
     */
    public function getPostedParameter($name)
    { 
        if($this->post==false)
        {
            return null;
        }
        if(array_key_exists($name, $this->post))
        {
            return $this->post[$name];
        }
        return null;
    }
    
    /**
     * Get a GET paramater
     * @param type $name
     * @return type 
     */
    public function getParameter($name)
    {
        if($this->get==false)
        {
            return null;
        }
        if(array_key_exists($name, $this->get))
        {
            return $this->get[$name];
        }
        return null;
    }
    
    public function getParameters()
    {
        return $this->get;
    }
    
    public function getPostParameters()
    {
        return $this->post;
    }
}

?>
