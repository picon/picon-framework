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
 * A link which will not direct to a page but submit an ajax request.
 * The callback method will be invoked when this happens to allow
 * a response to be created
 *
 * @author Martin Cassidy
 * @package web/ajax/markup/html
 */
class AjaxLink extends AbstractLink implements CallDecoratorWrapper
{
    private $callback;
    private $eventBehaviour;
    
    public function __construct($id, $callback, $callDecorator = null)
    {
        parent::__construct($id);
        Args::callBackArgs($callback, 1, 'callback');
        $this->eventBehaviour = new AjaxEventBehaviour('onclick', $callback);
        $this->add($this->eventBehaviour);
        $this->callback = $callback;
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->put('href', 'javascript:;');
    }
    
    //TODO not good that this is here and does nothing, refactor a bit to get rid of it
    protected function onLinkClicked()
    {
        
    }
    
    public function setAjaxCallDecorator(AjaxCallDecorator &$decorator)
    {
        $this->eventBehaviour->setAjaxCallDecorator($decorator);
    }
}

?>
