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
            return new PageRequestTarget(PiconApplication::get()->getHomePage());
        }
        else
        {
            return new PageRequestTarget($this->getPageClassForPath($request));
        }
    }
    
    public function matches(Request $request)
    {
        return $this->isHomePage($request) || $this->getPageClassForPath($request)!=false;
    }

    private function isHomePage(Request $request)
    {
        return preg_match("/^".$request->getRootPath()."\/?$/", $request->getPath());
    }

    private function getPageClassForPath(Request $request)
    {
        $mapEntry = PageMap::getPageMap();
        
        foreach($mapEntry as $path => $pageClass)
        {
            if(preg_match("/^".$request->getRootPath()."\/".$path."{1}\/?$/", $request->getPath()))
            {
                return $pageClass;
            }
        }
        return false;
    }
}

?>
