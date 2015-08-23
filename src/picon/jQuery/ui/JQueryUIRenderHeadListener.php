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

namespace picon\jquery\ui;
use picon\web\listeners\component\ComponentRenderHeadListener;
use picon\web\markup\html\HeaderContainer;
use picon\web\request\HeaderResponse;
use picon\web\ResourceReference;

/**
 * Render head listener for render jquery ui js and css
 *
 * @author Martin Cassidy
 * @package web/jquery/ui
 */
class JQueryUIRenderHeadListener implements ComponentRenderHeadListener
{   
    public function onHeadRendering(HeaderContainer &$container, HeaderResponse &$response)
    {
        $response->renderJavaScriptResourceReference(new ResourceReference('jquery-ui.js', AbstractJQueryUIBehaviour::getIdentifier()));
        $response->renderCSSResourceReference(new ResourceReference('jquery-ui.css', AbstractJQueryUIBehaviour::getIdentifier()));
    }
}

?>
