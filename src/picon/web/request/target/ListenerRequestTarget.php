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

use picon\Identifiable;
use picon\Identifier;

/**
 * Request target for invoking listeners
 *
 * @author Martin Cassidy
 * @package web/request/target
 */
class ListenerRequestTarget implements RequestTarget, Identifiable
{
    private $componentPath;
    private $page;
    private $behaviour;

    /**
     * @param string $page The name of the page
     * @param string $componentPath The path to the listener component
     * @param null $behaviour
     */
    public function __construct($page, $componentPath, $behaviour = null)
    {
        $this->page = $page;
        $this->componentPath = $componentPath;
        $this->behaviour = $behaviour;
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

        if($page==null)
        {
            $GLOBALS['requestCycle']->addTarget(new PageNotFoundRequestTarget());
            return;
        }
        
        $listener = $this->getListener($page);
        
        if($listener==null)
        {
            $page->beforePageRender();
            $listener = $this->getListener($page);
        }
        if($listener==null || !($listener instanceof Listener))
        {
            throw new \RuntimeException(sprintf("Listener component %s was not found", $this->componentPath));
        }

        if($GLOBALS['requestCycle']->getRequest()->isAjax()==false)
        {
            $url = $GLOBALS['requestCycle']->generateUrl(new PageInstanceRequestTarget($page));
            PageMap::get()->addOrUpdate($page);
            $GLOBALS['requestCycle']->addTarget(new RedirectRequestTarget($url));
        }
        $listener->onEvent();
    }
    
    private function getListener($page)
    {
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
        return $listener;
    }
    
    public function getComponentPath()
    {
        return $this->componentPath;
    }

    public function getPage()
    {
        return $this->page;
    }
    
    public function getBehaviour()
    {
        return $this->behaviour;
    }
    
    public static function getIdentifier()
    {
        return Identifier::forName(get_called_class());
    }
}

?>
