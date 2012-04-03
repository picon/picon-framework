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
 * A mark-up source is required to load mark-up for a component and also
 * enables callbacks for rendering steps (tag, body and head) to customize if
 * needed
 * 
 * @author Martin Cassidy
 * @package web/markup/sources
 */
interface MarkupSource
{
    function getMarkup(MarkupContainer $container, Component $child);
    
    function onComponentTag(Component $component, ComponentTag &$tag);
    
    function onComponentTagBody(Component $component, ComponentTag &$tag);
    
    function renderHead(Component $component, HeaderContainer $headerContainer, HeaderResponse $headerResponse);
}

?>
