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
namespace picon\web;

/**
 * Convience class for accessing information about a requests
 * This also provideds convience methods for analysing the request to 
 * determin what kind of request it is
 * @package web/request
 * @todo lots more to add in here
 * @author Martin Cassidy
 */
interface Request
{    
    public function getQueryString();
    
    public function getPath();
    
    public function isAjax();
    
    public function isResourceRequest();
    
    public function getRootPath();
    
    public function isPost();
    
    public function isGet();
    
    /**
     * Get a POST parameter
     * @param type $name
     * @return type 
     */
    public function getPostedParameter($name);
    
    /**
     * Get a GET paramater
     * @param type $name
     * @return type 
     */
    public function getParameter($name);
    
    public function getParameters();
    
    public function getPostParameters();
}

?>
