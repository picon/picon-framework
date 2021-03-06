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
 * A drop down choice HTML select element
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class DropDown extends AbstractSingleChoice
{
    public function __construct($id, $choices, ChoiceRenderer $choiceRenderer = null, Model $model = null, $disabled = null)
    {
        parent::__construct($id, $choices, $choiceRenderer, $model, $disabled);
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        $this->checkComponentTag($tag, 'select');
        $tag->setTagType(new XmlTagType(XmlTagType::OPEN));
        parent::onComponentTag($tag);
    }
    
    protected function onComponentTagBody(ComponentTag $tag)
    {
        $value = $this->getValue();
        if($this->isRequired() && empty($value) || !$this->isRequired())
        {
            $this->renderOption($this->getDefaultValue(), null, empty($value));
        }
        
        $this->renderOptions();
    }
    
    protected function getDefaultValue()
    {
        return $this->getLocalizer()->getString('default');
    }
    
    protected function supportNestedArray()
    {
        return true;
    }
}

?>
