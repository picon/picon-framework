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

use picon\MarkupUtils;
use picon\PiconApplication;
use picon\web\request\HeaderResponse;

/**
 * Represents the &lt;head&gt; element
 *
 * @author Martin Cassidy
 * @package web/markup/html
 */
class HeaderContainer extends TransparentMarkupContainer
{
    public function __construct($id, Model $model = null)
    {
        parent::__construct($id, $model);
        $this->setRenderBodyOnly(true);
    }
    
    protected function onComponentTagBody(ComponentTag $tag)
    {
        $this->getResponse()->write('<head>');
        parent::onComponentTagBody($tag);
        $page = $this->getPage();
        $headerResponse = new HeaderResponse($this->getResponse());
        PiconApplication::get()->getComponentRenderHeadListener()->onHeadRendering($this, $headerResponse);
        $page->renderHead($headerResponse);
        $self = $this;
        $callback = function(Component &$component) use($headerResponse, $self)
        {
            $component->renderHeadContainer($self, $headerResponse);
            return Component::VISITOR_CONTINUE_TRAVERSAL;
        };
        
        $page->visitChildren(Component::getIdentifier(), $callback);
        $this->getResponse()->write('</head>');
    }
    
    public function getMarkup()
    {
        $parent = $this->getParent();
        
        if($parent==null)
        {
            throw new \RuntimeException('Unable to locate parent for header container');
        }
        
        $headerMarkup = $parent->getMarkup()->getChildByName('head');
        
        if($headerMarkup!=null)
        {
            return $headerMarkup;
        }
        
        $headerMarkup = MarkupUtils::findPiconTag('head', $parent->getMarkup());
        return $headerMarkup;         
    }
    
}

?>
