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

use picon\Identifier;

/**
 * Stateless page request that will retrieve and invoke a listener on the page
 *
 * @author Martin Cassidy
 * @package web/request/target
 */
class PageRequestWithListenerTarget extends PageRequestTarget
{
    private $componentPath;
    private $behaviour;

    /**
     * @param Identifier $pageClass
     * @param $componentPath
     * @param null $behaviour
     */
    public function __construct(Identifier $pageClass, $componentPath, $behaviour = null)
    {
        parent::__construct($pageClass);
        $this->componentPath = $componentPath;
        $this->behaviour = $behaviour;
    }
    
    public function respond(Response $response)
    {
        $fullClassName = $this->getPageClass()->getFullyQualifiedName();
        $page = new $fullClassName();
        $page->beforePageRender();
        
        $listener = null;
        
        if($this->behaviour==null)
        {
            $listener = $page->get($this->componentPath);
        }
        else
        {
            $component = $page->get($this->componentPath);
            if($component!=null && $component instanceof Component)
            {
                $listener = $component->getBehaviourById($this->behaviour);
            }
        }
        
        if($listener==null)
        {
            throw new \RuntimeException(sprintf("Listener component %s was not found", $this->componentPath));
        }
        
        $listener->onEvent();
        $page->renderPage();
        $response->flush();
    }
    
    public function getComponentPath()
    {
        return $this->componentPath;
    }
    
    public function getBehaviour()
    {
        return $this->behaviour;
    }
}

?>
