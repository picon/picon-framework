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

namespace picon\web\markup\resolver;
use picon\web\domain\ComponentTag;
use picon\web\MarkupContainer;

/**
 * All picon tags (e.g. picon:head or picon:child) are component tags, but need
 * not have a component created in the hierarchy for them by the user as a 
 * resolve will create one automatically during the render process
 * @author Martin Cassidy 
 * @package web/markup/resolver
 */
interface ComponentResolver
{
    function resolve(MarkupContainer $container, ComponentTag &$tag);
}

?>
