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
 * Description of DropDown
 * 
 * @author Martin Cassidy
 */
class DropDown extends AbstractChoice
{
    private $choiceRenderer;
    private $choiceReferences;
    private $outputPair;
    
    public function __construct($id, $choices, ChoiceRenderer $choiceRenderer = null, Model $model = null)
    {
        parent::__construct($id, $choices, $model);
        if($choiceRenderer==null)
        {
            $choiceRenderer = new ChoiceRenderer();
        }
        $this->choiceRenderer = $choiceRenderer;
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        $this->checkComponentTag($tag, 'select');
        parent::onComponentTag($tag);
    }
    
    protected function isSelected($choice)
    {
        if($this->getRawInput()==null)
        {
            return $this->choiceRenderer->getValue($choice)==$this->getModelObjectAsString();
        }
        else
        {
            return $this->getRawInput()==$this->choiceRenderer->getValue($choice);
        }
    }
    
    private function preprareChoiceList()
    {
        foreach($this->getChoices() as $choice)
        {
            $value = $this->choiceRenderer->getValue($choice);
            $name = $this->choiceRenderer->getDisplay($choice);
            $this->outputPair[$value] = $name;
            $this->choiceReferences[$value] = $choice;
        }
    }
    
    protected function onComponentTagBody(ComponentTag $tag)
    {
        $this->preprareChoiceList();
        foreach($this->outputPair as $value => $name)
        {
            echo '<option';
            
            if($this->isSelected($this->choiceReferences[$value]))
            {
                echo ' selected="selected"';
            }
            echo ' value="'.$value.'"';
            
            echo '>';
            echo $name;
            echo '</option>';
        }
    }
    
    public function processInput()
    {
        $selected = $this->getRawInput();
        $this->updateModel($this->choiceReferences[$selected]);
    }
}

?>
