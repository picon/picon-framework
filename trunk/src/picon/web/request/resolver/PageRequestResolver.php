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
 * Description of PageRequestResolver
 * 
 * @author Martin Cassidy
 */
class PageRequestResolver implements RequestResolver
{
    public function resolve(Request $request)
    {
        if($this->isHomePage($request))
        {
            $homepage = PiconApplication::get()->getHomePage();
            return $this->checkListeners($homepage::getIdentifier());
        }
        else
        {
            return $this->checkListeners($this->getPageClassForPath($request));
        }
    }
    
    public function matches(Request $request)
    {
        return $this->isHomePage($request) || $this->getPageClassForPath($request)!=false;
    }

    private function checkListeners($page)
    {
        //@todo get params from request
        if(isset($_GET['listener']))
        {
            return new PageRequestWithListenerTarget($page, $_GET['listener']);
        }
        else
        {
            return new PageRequestTarget($page);
        }
    }
    
    /**
     *
     * @param Request $request
     * @todo alter expression to handle page params
     * @return type 
     */
    private function isHomePage(Request $request)
    {
        return preg_match("/^".$request->getRootPath()."\/{1}([?|&]{1}\\S+={1}\\S+)*$/", $request->getPath());
    }

    /**
     * @param Request $request
     * @todo alter expression to handle page params
     * @return type 
     */
    private function getPageClassForPath(Request $request)
    {
        $mapEntry = PageMap::getPageMap();
        
        foreach($mapEntry as $path => $pageClass)
        {
            if(preg_match("/^".$request->getRootPath()."\/".$path."{1}([?|&]{1}\\S+={1}\\S+)*$/", $request->getPath()))
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
     * @return string the URL for the request target
     */
    public function generateUrl(RequestTarget $target)
    {
        if($target instanceof PageRequestWithListenerTarget)
        {
            return str_replace("\\", "/", $target->getPageClass()->namespace).$target->getPageClass()->className.'?listener='.$target->getComponentPath();
        }
        else
        {
            //@todo turn this into an absolute url
            return $target->getPageClass();
        }
    }
    
    public function handles(RequestTarget $target)
    {
        return $target instanceof PageRequestTarget;
    }
}

?>
