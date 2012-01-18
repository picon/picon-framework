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
 * Ajax implementation to add an ajax callback to a component when a javascript
 * event is fired e.g. onclick
 *
 * @author Martin Cassidy
 * @package web/ajax
 */
class AjaxEventBehaviour extends AbstractAjaxBehaviour
{
    private $event;
    private $callback;
    
    public function __construct($event, $callback)
    {
        parent::__construct();
        Args::isString($event, 'event');
        Args::callBackArgs($callback, 1, 'callback');
        $this->event = $event;
        $this->callback = $callback;
    }
    
    public function onComponentTag(Component &$component, ComponentTag &$tag)
    {
        parent::onComponentTag($component, $tag);
        $tag->put($this->event, $this->generateCallbackScript());
    }
    
    public function onEventCallback(AjaxRequestTarget $target)
    {
        $callable = $this->callback;
        $callable($target);
    }
    
    protected function callScript()
    {
        return "";
    }
    
    protected function successScript()
    {
        return "";
    }
    
    protected function failScript()
    {
        return "";
    }
}

?>
