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

/**
 * Source for border components
 * 
 * @author Martin Cassidy
 * @package web/markup/sources
 */
class BorderMarkupSourcingStratagy extends AbstractAssociatedMarkupSource
{
    public function onComponentTagBody(Component $component, ComponentTag &$tag)
    {
        $borderMarkup = $component->loadAssociatedMarkup();
        $border = MarkupUtils::findPiconTag('border', $borderMarkup, $component);
        
        if($border==null)
        {
            throw new \MarkupNotFoundException(sprintf("Found markup for border %s however there is no picon:border tag.", $component->getId(0)));
        }
        
        $body = MarkupUtils::findPiconTag('body', $borderMarkup);
        
        if($body==null)
        {
            throw new \RuntimeException('No picon:body tag was found in border.');
        }
        
        $body->setChildren($tag->getChildren());
        $tag->setChildren(array($border));
    }
    
    public function getRootTag(MarkupElement $markup)
    {
        return MarkupUtils::findPiconTag('border', $markup);
    }
}

?>
