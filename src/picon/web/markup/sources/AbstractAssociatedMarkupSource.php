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

use picon\core\utils\MarkupUtils;
use picon\web\request\HeaderResponse;

/**
 * Mark-up source for components which hav associated mark-up
 * 
 * @author Martin Cassidy
 * @package web/markup/sources
 */
abstract class AbstractAssociatedMarkupSource extends AbstractMarkupSource
{
    public function getMarkup(MarkupContainer $container, Component $child)
    {
        $markup = $container->loadAssociatedMarkup();
        $root = $this->getRootTag($markup);
        return MarkupUtils::findComponentTag($root, $child->getId(), $container);
    }
    
    public function renderHead(Component $component, HeaderContainer $headerContainer, HeaderResponse $headerResponse)
    {
        $markup = $component->loadAssociatedMarkup();
        $heads = $this->findHead(array($markup));

        foreach($heads as $index => $head)
        {
            $id = $component->getId().'_head_'.$index;
            $headerPart = new HeaderPartContainer($id, $head);
            $headerPart->render();
        }
    }
    
    private function findHead($markup)
    {
        $heads = array();
        foreach($markup as $element)
        {
            if($element instanceof PiconTag && $element->isHeaderTag())
            {
                array_push($heads, $element);
            }
            if($element instanceof MarkupElement && $element->hasChildren())
            {
                $heads = array_merge($heads, $this->findHead($element->getChildren()));
            }
        }
        return $heads;
    }
    
    public abstract function getRootTag(MarkupElement $markup);
}

?>
