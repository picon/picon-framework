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

use picon\core\Types;
use picon\XmlTagType;

/**
 * A text area form component
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class TextArea extends AbstractTextComponent
{
    protected function onComponentTagBody(ComponentTag $tag)
    {
        $this->getResponse()->write($this->getValue());
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        $tag->setTagType(new XmlTagType(XmlTagType::OPEN));
        $this->checkComponentTag($tag, 'textarea');
        parent::onComponentTag($tag);
    }
    
    /**
     * Get the data type for this text component
     */
    protected function getType()
    {
        return Types::TYPE_STRING;
    }
}

?>
