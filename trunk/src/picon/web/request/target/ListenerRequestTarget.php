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
 * Description of ListenerRequestTarget
 *
 * @author Martin
 */
class ListenerRequestTarget implements RequestTarget
{
    private $componentPath;
    private $page;
    
    /**
     *
     * @param string $page The name of the page 
     * @param type $componentPath The path to the listener component
     */
    public function __construct($page, $componentPath)
    {
        $this->page = $page;
        $this->componentPath = $componentPath;
    }
    
    public function respond(Response $response)
    {
        if($this->page instanceof Identifier)
        {
            $fullClassName = $this->page->getFullyQualifiedName();
            $page = new $fullClassName();
            $page->internalInitialize();
            
        }
        else
        {
            $page = $this->page;

        }
        $page->beforePageRender();
        $listener = $page->get($this->componentPath);
        if($listener==null)
        {
            throw new \RuntimeException(sprintf("Listener component %s was not found", $this->componentPath));
        }
        $GLOBALS['requestCycle']->addTarget(new PageInstanceRequestTarget($page));
        $listener->onEvent();
    }
    
    public function getComponentPath()
    {
        return $this->componentPath;
    }

    public function getPage()
    {
        return $this->page;
    }
}

?>
