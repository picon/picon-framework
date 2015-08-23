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

namespace picon\web\markup\html\form;
use picon\web\markup\html\basic\Label;

/**
 * A label which can be associated with a form component so that 
 * the <b>for</b> attribute can be added to the output
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class FormComponentLabel extends Label
{
    private $source;
    
    public function __construct($id, LabeledMarkupContainer $source)
    {
        parent::__construct($id);
        $this->source = $source;
        $this->source->setOutputMarkupId(true);
    }
    
    protected function onComponentTagBody(\picon\web\ComponentTag $tag)
    {
        $this->getResponse()->write($this->source->getLabel());
    }
    
    protected function onComponentTag(\picon\web\ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->put('for', $this->source->getMarkupId());
    }
}

?>
