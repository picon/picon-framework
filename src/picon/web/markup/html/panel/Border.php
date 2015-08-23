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
use picon\web\markup\sources\BorderMarkupSourcingStratagy;
use picon\web\MarkupContainer;
use picon\web\markup\html\TransparentMarkupContainer;

/**
 * A border works in the same way as a panel but does ont replace the origonal 
 * content of the HTML tag it is added to.
 * 
 * The border mark-up:
 * 
 * &lt;picon:border&gt;
 * &lt;h2&gt;border heading&lt;/h2&gt;
 * &lt;picon:body /&gt;
 * &lt;p&gt;other border content&lt;/p&gt;
 * &lt;/picon:border&gt;
 * 
 * And in the composing class:
 * 
 * &lt;h1&gt;heading&lt;/h1&gt;
 * &lt;div picon:id="myBorder"&gt;
 * Border content will wrap this content
 * &lt;/div&gt;
 * &lt;p&gt;other mark-up...&lt;/p&gt;
 * 
 * Would be rendered as:
 * 
 * &lt;h1&gt;heading&lt;/h1&gt;
 * &lt;div picon:id="myBorder"&gt;
 * &lt;h2&gt;border heading&lt;/h2&gt;
 * Border content will wrap this content
 * &lt;p&gt;other border content&lt;/p&gt;
 * &lt;/div&gt;
 * &lt;p&gt;other mark-up...&lt;/p&gt;
 * 
 * @author Martin Cassidy
 * @package web/markup/html/panel
 */
class Border extends MarkupContainer
{
    protected function getMarkUpSource()
    {
        return new BorderMarkupSourcingStratagy();
    }
    
    public function getBorderBody()
    {
        return new TransparentMarkupContainer('picon_body');
    }
}

?>
