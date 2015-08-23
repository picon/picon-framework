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

use picon\core\PiconApplication;
use picon\jquery\AbstractJQueryBehaviour;

/**
 * Adds on the jquery ui resources through a render head listener
 * @todo add support fo the jquery ui css
 * @author Martin Cassidy
 * @package web/jquery/ui
 */
abstract class AbstractJQueryUIBehaviour extends AbstractJQueryBehaviour
{
    public function __construct()
    {
        parent::__construct();
        PiconApplication::get()->addComponentRenderHeadListener(new JQueryUIRenderHeadListener());
    }
    
    /**
     * @todo This is a bad way of forcing listeners to re register
     */
    public function __wakeup()
    {
        parent::__wakeup();
        PiconApplication::get()->addComponentRenderHeadListener(new JQueryUIRenderHeadListener());
    }
}

?>
