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
 * A select multiple box where multiple choices can be chosen
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class ListMultiple extends AbstractMultipleChoice
{
    private $rows;
    public function __construct($id, $choices, $rows = 5, ChoiceRenderer $choiceRenderer = null, Model $model = null)
    {
        parent::__construct($id, $choices, $choiceRenderer, $model);
        $this->rows = $rows;
    }
    
    protected function validateModel()
    {
        $object = $this->getModelObject();
        if($object!=null && !is_array($object))
        {
            throw new \IllegalStateException('ListMultiple must have an array model');
        }
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        $this->checkComponentTag($tag, 'select');
        $this->checkComponentTagAttribute($tag, "multiple", "multiple");
        $tag->put('size', $this->rows);
        parent::onComponentTag($tag);
    }
    
    protected function onComponentTagBody(ComponentTag $tag)
    {
        $this->renderOptions();
    }
    
    protected function convertInput()
    {
        $selection = array();
        foreach($this->getChoices() as $index => $choice)
        {
            foreach($this->getRawInputArray() as $value)
            {
                if($this->valueForChoice($choice, $value, $index))
                {
                    array_push($selection, $choice);
                }
            }
        }
        $this->setConvertedInput($selection);
    }
}

?>
