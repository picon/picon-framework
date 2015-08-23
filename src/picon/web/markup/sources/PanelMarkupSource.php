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

/**
 * A mark-up source for panels
 * 
 * @author Martin Cassidy
 * @package web/markup/sources
 */
class PanelMarkupSource extends AbstractAssociatedMarkupSource
{
    public function onComponentTagBody(Component $component, ComponentTag &$tag)
    {
        $panelMarkup = $component->loadAssociatedMarkup();
        $panel = MarkupUtils::findPiconTag('panel', $panelMarkup, $component);
        
        if($panel==null)
        {
            throw new \picon\core\exceptions\MarkupNotFoundException(sprintf("Found markup for panel %s however there is no picon:panel tag.", $component->getId(0)));
        }
        
        $tag->setChildren(array($panel));
       
    }
    
    public function getRootTag(MarkupElement $markup)
    {
        return MarkupUtils::findPiconTag('panel', $markup);
    }
}

?>
