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
 * Resolves requests for listener callbacks
 * 
 * @author Martin Cassidy
 */
class ListenerRequestResolver implements RequestResolver
{
    public function matches(Request $request)
    {
        return array_key_exists('listener', $request->getParameters());
    }
    
    public function resolve(Request $request)
    {
        if(array_key_exists('pageid', $request->getParameters()))
        {
            $page = PageMap::get()->getPageById($request->getParameter('pageid'));
            if($page!=null)
            {
                return new ListenerRequestTarget($page, $request->getParameter('listener'));
            }
            return new PageRequestTarget(\SessionExpiredPage::getIdentifier());
        }
        else
        {
            return new ListenerRequestTarget($this->getPageClassForPath($request), $request->getParameter('listener'));
        }
        
    }
    
    /**
     * @param Request $request
     * @todo alter expression to handle page params
     * @todo this is duplicated from PageRequestTarget, needs refactoring
     * @return type 
     */
    private function getPageClassForPath(Request $request)
    {
        $mapEntry = PageMap::getPageMap();
        
        foreach($mapEntry as $path => $pageClass)
        {
            if(preg_match("/^".$this->prepare($request->getRootPath())."\/".$path."{1}([?|&]{1}\\S+={1}\\S+)*$/", $request->getPath()))
            {
                return $pageClass::getIdentifier();
            }
        }
        return false;
    }
    
    public function generateUrl(RequestTarget $target)
    {
        throw new \NotImplementedException();
    }
    
    public function handles(RequestTarget $target)
    {
        return false;
    }
    
    private function prepare($value)
    {
        return str_replace('/', "\\/", $value);
    }
}

?>
