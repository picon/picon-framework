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
 * Description of WebPage
 * 
 * @author Martin Cassidy
 * @package web
 */
class WebPage extends MarkupContainer implements RequestablePage
{
    public function __construct()
    {
        parent::__construct(null);
    }
    
    protected function onRender()
    {
        parent::renderAll();
    }
    
    public function renderPage()
    {
        $this->render();
    }
    
    public function isPageStateless()
    {
        $stateless = true;
        $callback = function($component) use (&$stateless)
        {
            if(!$component->isStateless())
            {
                $stateless = false;
                return new VisitorResponse(VisitorResponse::STOP_TRAVERSAL);
            }
            return new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL);
        };
        $this->visitChildren(Component::getIdentifier(), $callback);
        return $stateless;
    }
}

?>
