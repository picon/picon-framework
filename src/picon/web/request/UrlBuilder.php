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
 * Helper class for building URLs
 * @todo can this use the psr one?
 * @author Martin Cassidy
 * @package web/request
 */
class UrlBuilder
{
    private $protocol = "http";
    private $url;
    private $path;
    private $params = array();
            
    /**
     * Create new url builder for the base URL. Default protocol is HTTP
     * @param string $baseURL
     */
    private function __construct($baseURL)
    {
        $this->url = $baseURL;
    }
    
    /**
     * Set the protocol
     * @param string $protocol
     */
    public function protocol($protocol)
    {
        $this->protocol = $protocol;
    }
    
    /**
     * Set the path. Leading slash will be removed. Trailing slash is optional
     * @param string $path
     */
    public function path($path)
    {
        if(substr($path, 0,1)=="/")
        {
            $path = substr($path, 1, strlen($path));
        }
        $this->path = $path;
    }
    
    /**
     * Add a new query string parameter
     * @param string $name
     * @param string $value
     */
    public function parameter($name, $value)
    {
        $params = &$this->params;
        $params[$name] = $value;
    }
    
    /**
     * Build the URL into a string
     * @return string
     */
    public function build()
    {
        $url = sprintf("%s://%s%s%s", $this->protocol, $this->url, strlen($this->path)==0?"":"/", $this->path);
        
        $i = 0;
        foreach($this->params as $name => $value)
        {
            $url .= sprintf("%s%s=%s", $i==0?"?":"&", $name, $value);
            $i++;
        }
        
        return $url;
    }
    
    /**
     * Create a new URL builder
     * @param $baseURL
     * @return UrlBuilder
     */
    public static function url($baseURL)
    {
        $builder = new UrlBuilder($baseURL);
        return $builder;
    }
}

?>
