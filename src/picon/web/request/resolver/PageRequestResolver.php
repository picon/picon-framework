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

use picon\core\PiconApplication;

/**
 * Basic stateless page request resolver
 * 
 * @author Martin Cassidy
 * @package web/request/resolver
 */
class PageRequestResolver implements RequestResolver
{
    public function resolve(Request $request)
    {
        if($this->isHomePage($request))
        {
            $homepage = PiconApplication::get()->getHomePage();
            return new PageRequestTarget($homepage::getIdentifier());
        }
        else
        {
            return new PageRequestTarget($this->getPageClassForPath($request));
        }
    }
    
    public function matches(Request $request)
    {
        return ($this->isHomePage($request) || $this->getPageClassForPath($request)!=false) && $request->getParameter('picon-resource')==null && $request->getParameter('listener')==null && $request->getParameter('pageid')==null;
    }
    
    /**
     *
     * @param Request $request
     * @todo alter expression to handle page params
     * @return
     */
    private function isHomePage(Request $request)
    {
        return preg_match("/^".$this->prepare($request->getRootPath())."\/{1}([?|&]{1}\\S+={1}\\S+)*$/", $request->getPath());
    }

    /**
     * @param Request $request
     * @todo alter expression to handle page params
     * @return
     */
    private function getPageClassForPath(Request $request)
    {
        $mapEntry = PageMap::getPageMap();
        
        foreach($mapEntry as $path => $pageClass)
        {
            if(preg_match("/^".$this->prepare($request->getRootPath())."\/".str_replace("/", "\\/", $path)."{1}([?|&]{1}\\S+={1}\\S+)*$/", urldecode($request->getPath())))
            {
                return $pageClass::getIdentifier();
            }
        }
        return false;
    }
    
    /**
     *
     * @param RequestTarget $target
     * @todo Create a url builder helper
     * @todo turn this into an absolute url
     * @return string the URL for the request target
     */
    public function generateUrl(RequestTarget $target)
    {
        if($target instanceof PageRequestWithListenerTarget)
        {
            $trail = "";
            if($target->getPageClass()->namespace!=null)
            {
                $trail = '/';
            }
            $behaviourApped = null;
            if($target->getBehaviour()!=null)
            {
                $behaviourApped = '&behaviour='.$target->getBehaviour();
            }
            
            return $target->getPageClass()->namespace.$trail.$target->getPageClass()->className.'?listener='.$target->getComponentPath().$behaviourApped;
        }
        else if($target instanceof ListenerRequestTarget)
        {
            $ident = $target->getPage()->getIdentifier();
            $trail = "";
            if($ident->namespace!=null)
            {
                $trail = '/';
            }
            
            $behaviourApped = null;
            if($target->getBehaviour()!=null)
            {
                $behaviourApped = '&behaviour='.$target->getBehaviour();
            }
            
            return $ident->namespace.$trail.$ident->className.'?pageid='.$target->getPage()->getId().'&listener='.$target->getComponentPath().$behaviourApped;
        }
        else
        {
            $trail = "";
            if($target->getPageClass()->namespace!=null)
            {
                $trail = '/';
            }
            return $target->getPageClass()->namespace.$trail.$target->getPageClass()->className;
        }
    }
    
    public function handles(RequestTarget $target)
    {
        return $target instanceof PageRequestTarget || $target instanceof ListenerRequestTarget;
    }
    
    private function prepare($value)
    {
        return str_replace('/', "\\/", $value);
    }
}

?>
