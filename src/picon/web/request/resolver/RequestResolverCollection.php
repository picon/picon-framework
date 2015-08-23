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

namespace picon\web\request\resolver;
use picon\web\request\Request;
use picon\web\request\target\RequestTarget;
use picon\web\RequestResolver;

/**
 * Collection of resolvers that work as one
 * 
 * @author Martin Cassidy
 * @package web/request/resolver
 */
class RequestResolverCollection implements RequestResolver
{
    private $resolvers = array();
    
    public function __construct()
    {
        $this->add(new PageRequestResolver());
        $this->add(new PageInstanceRequestResolver());
        $this->add(new ListenerRequestResolver());
        $this->add(new ResourceRequestResolver);
    }
    
    public function add(RequestResolver $resolver)
    {
        array_push($this->resolvers, $resolver);
    }
    
    public function matches(Request $request)
    {
        foreach($this->resolvers as $resolver)
        {
            if($resolver->matches($request))
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     *
     * @param Request $request
     * @return RequestTarget the target that matches the request, null if none did 
     */
    public function resolve(Request $request)
    {
        foreach($this->resolvers as $resolver)
        {
            if($resolver->matches($request))
            {
                return $resolver->resolve($request);
            }
        }
        
        return null;
    }
    
    public function generateUrl(RequestTarget $target)
    {
        foreach($this->resolvers as $resolver)
        {
            if($resolver->handles($target))
            {
                $url = $resolver->generateUrl($target);
                if(!empty($url))
                {
                    return $url;
                }
            }
        }
        
        return null;
    }
    
    public function handles(RequestTarget $target)
    {
        foreach($this->resolvers as $resolver)
        {
            if($resolver->handles($target))
            {
                return true;
            }
        }
        return false;
    }
}

?>
