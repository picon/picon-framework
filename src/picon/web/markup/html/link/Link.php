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

namespace picon\web\markup\html\link;

use closure;
use picon\core\Args;
use picon\web\domain\ComponentTag;
use picon\web\domain\PopupSettings;

/**
 * Basic implementation of link
 * 
 * @author Martin Cassidy
 * @package web/markup/html/link
 */
class Link extends AbstractLink
{
    private $callback;
    private $popupSettings;
    
    public function __construct($id, closure $callback)
    {
        parent::__construct($id);
        Args::callBack($callback, 'callback');
        $this->callback = $callback;
    }
    
    protected function onLinkClicked()
    {
        $callable = $this->callback;
        $callable();
    }
    
    public function setPopupSettings(PopupSettings $settings)
    {
        $this->popupSettings = $settings;
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $url = $this->urlForListener($this);
        $tag->put('href', $this->popupSettings==null?$url:'javascript:;');

        if($this->popupSettings!=null)
        {
            $properties = $this->popupSettings;
            $tag->put('onClick', sprintf("javascript:window.open('%s','%s','width=%s,height=%s');", $url, $properties->name, $properties->width, $properties->height));
        }
    }
    
    protected function generateHref($url)
    {
        if($this->popupSettings==null)
        {
            return $url;
        }
        else
        {
            $properties = $this->popupSettings;
            return sprintf("javascript:window.open('%s','%s','width=%s,height=%s');", $url, $properties->name, $properties->width, $properties->height);
        }
    }
}

?>
