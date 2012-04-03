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
 * Adds on the jquery js resource through a listener
 *
 * @author Martin Cassidy
 * @package web/jQuery
 */
abstract class AbstractJQueryBehaviour extends AbstractBehaviour implements BehaviourListener
{
    public function __construct()
    {
        PiconApplication::get()->addComponentRenderHeadListener(new JQueryRenderHeadListener());
    }
    
    /**
     * @todo This is a bad way of forcing listeners to re register
     */
    public function __wakeup()
    {
        parent::__wakeup();
        PiconApplication::get()->addComponentRenderHeadListener(new JQueryRenderHeadListener());
    }
    
    /**
     * This ajax.js should NOT be rendered all the time
     * @param Component $component
     * @param HeaderContainer $headerContainer
     * @param HeaderResponse $headerResponse 
     */
    public function renderHead(Component &$component, HeaderContainer $headerContainer, HeaderResponse $headerResponse)
    {
        parent::renderHead($component, $headerContainer, $headerResponse);
        $headerResponse->renderJavaScriptResourceReference(new ResourceReference('ajax.js', AbstractAjaxBehaviour::getIdentifier()));
    }
    
    public function isStateless()
    {
        return false;
    }
}

?>
