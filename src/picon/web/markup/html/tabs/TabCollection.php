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

use picon\ComonDomainBase;

use picon\Args;

/**
 * An array of tabs which are added to a tab panel
 * 
 * @author Martin Cassidy
 * @package web/markup/html/tabs
 */
class TabCollection extends ComonDomainBase
{
    private $tabs = array();
    
    public function __construct($tabs = array())
    {
        $this->tabs = $tabs;
    }
    
    /**
     *
     * @param mixed $tab
     * @param callable $newMethod
     */
    public function addTab($tab, $newMethod = null)
    {
        if($tab instanceof Tab)
        {
            array_push($this->tabs, $tab);
        }
        else
        {
            Args::isString($tab, 'tab');
            Args::callBackArgs($newMethod, 1, 'newMethod');
            array_push($this->tabs, new Tab($tab, $newMethod));
        }
    }
}

?>
