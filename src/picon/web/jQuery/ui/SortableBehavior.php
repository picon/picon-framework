<?php

/**
 * Podium CMS
 * http://code.google.com/p/podium/
 *
 * Copyright (C) 2011-2012 Martin Cassidy <martin.cassidy@webquub.com>

 * Podium CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Podium CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Podium CMS.  If not, see <http://www.gnu.org/licenses/>.
 * */

namespace picon\web;

/**
 * Behavior to add on jQuery UI sortable functionality
 * 
 * @todo finish off remaining options
 * @todo create a better callback procedure for js code
 * @author Martin Cassidy
 * @package web/jQuery/ui
 */
class SortableBehavior extends DefaultJQueryUIBehaviour
{
    public function __construct()
    {
        parent::__construct('sortable');
    }
    
    public function setReceiveCallback($receiveCallback, $jsCode = '')
    {
        Args::callBackArgs($receiveCallback, 1, 'receiveCallback');
        $this->getOptions()->add(new CallbackFunctionOption('receive', $receiveCallback, $jsCode, 'event', 'ui'));
    }
    
    public function setStopCallback($stopCallback, $jsCode = '')
    {
        Args::callBackArgs($stopCallback, 1, 'stopCallback');
        $this->getOptions()->add(new CallbackFunctionOption('stop', $stopCallback, $jsCode, 'event', 'ui'));
    }
    
    public function setForcePlaceHolderSize($force)
    {
        Args::isBoolean($force, 'force');
        $this->getOptions()->add(new BooleanOption('forcePlaceholderSize', $force));
    }
}

?>
