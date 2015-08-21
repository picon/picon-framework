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

use picon\XmlTagType;

/**
 * A label is a very simple text based component that can work with almost 
 * an HTML tag. It is used for placing the contents of a model 
 * into the body of the associated tag.
 *
 * @author Martin Cassidy
 * @package web/markup/html/basic
 */
class Label extends WebComponent
{
    public function __construct($id, Model $model = null)
    {
        parent::__construct($id,$model);
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->setTagType(new XmlTagType(XmlTagType::OPEN));
    }
    
    protected function onComponentTagBody(ComponentTag $tag)
    {
        $this->getResponse()->write($this->getModelObjectAsString());
    }
}

?>
