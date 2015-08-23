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

namespace picon\web\markup\html\panel;
use picon\web\markup\sources\PanelMarkupSource;
use picon\web\MarkupContainer;

/**
 * A panel allows for a block of mark-up to be encapsulated into reusable 
 * components. A panel consists of an HTML file and a class containing the 
 * code to drive it. Panel can be rendered on any HTML tag.
 * 
 * The panel mark-up:
 * &lt;picon:panel&gt;
 * &lt;h2&gt;panel heading&lt;/h2&gt;
 * &lt;/picon:panel&gt;
 * 
 * And in the composing class:
 * &lt;h1&gt;heading&lt;/h1&gt;
 * &lt;div picon:id="myPanel"&gt;
 * Panel content will appear here
 * &lt;/div&gt;
 * &lt;p&gt;other mark-up...&lt;/p&gt;
 * 
 * Would be rendered as:
 * &lt;h1&gt;heading&lt;/h1&gt;
 * &lt;div picon:id="myPanel"&gt;
 * &lt;h2&gt;panel heading&lt;/h2&gt;
 * &lt;/div&gt;
 * &lt;p&gt;other mark-up...&lt;/p&gt;
 * 
 * @author Martin Cassidy
 * @package web/markup/html/panel
 */
class Panel extends MarkupContainer
{
    protected function newMarkupSource()
    {
        return new PanelMarkupSource();
    }
}

?>
