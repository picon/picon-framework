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

use picon\core\Args;

/**
 * A form component which contains a pre-defined list of
 * posible choices
 * 
 * @todo implement disabled for all abstract choice components
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
abstract class AbstractChoice extends FormComponent
{
    private $choiceRenderer;
    
    private $choices;
    
    private $isDisabled;
    
    /**
     *
     * @param string $id
     * @param array $choices
     * @param Model $model
     * @param callable $isDisabled
     */
    public function __construct($id, $choices, ChoiceRenderer $choiceRenderer = null, Model $model = null, $isDisabled = null)
    {
        parent::__construct($id, $model);
        Args::isArray($choices, 'choices');
        
        $this->choices = $choices;
        
        if($choiceRenderer==null)
        {
            $choiceRenderer = new ChoiceRenderer();
        }
        $this->choiceRenderer = $choiceRenderer;
        
        if($isDisabled!=null)
        {
            Args::callBackArgs($isDisabled, 2, 'isDisabled');
            $this->isDisabled = $isDisabled;
        }
    }
    
    protected function getChoiceRenderer()
    {
        return $this->choiceRenderer;
    }
    
    protected function getChoices()
    {
        return $this->choices;
    }
    
    public abstract function isSelected($choice, $index);

    /**
     * Render an <option> element
     * @param $name
     * @param $value
     * @param $selected
     * @param $disabled
     */
    protected function renderOption($name, $value, $selected, $disabled)
    {
        $this->getResponse()->write('<option');
        if($selected)
        {
             $this->getResponse()->write(' selected="selected"');
        }
        if($disabled)
        {
             $this->getResponse()->write(' disabled="disabled"');
        }
        $this->getResponse()->write(' value="'.htmlentities($value).'"');
        
        $this->getResponse()->write('>');
        $this->getResponse()->write(htmlentities($name));
        $this->getResponse()->write('</option>');
    }
    
    protected function renderOptGroup($name, $options, $index)
    {
        $this->getResponse()->write('<optgroup');
        $this->getResponse()->write(' label="'.htmlentities($name).'"');
        $this->getResponse()->write('>');
        $this->renderOptions($options, $index);
        $this->getResponse()->write('</optgroup>');
    }
    
    protected function renderOptions($choices = null, $outerIndex = 1)
    {
        if($choices==null)
        {
            $choices = $this->choices;
        }
        $i = 1;
        foreach($choices as $index => $choice)
        {
            $actualIndex = $i * $outerIndex;
            if(is_array($choice))
            {
                $this->renderOptGroup($index, $choice, $i);
            }
            else
            {
                $selected = $this->isSelected($choice, $actualIndex);
                $disabled = $this->isOptionDisabled($choice, $actualIndex);
                $this->renderOption($this->choiceRenderer->getDisplay($choice, $actualIndex), $this->choiceRenderer->getValue($choice, $actualIndex), $selected, $disabled);
            }
            $i++;
        }
    }
    
    protected function isOptionDisabled($choice, $index)
    {
        if($this->isDisabled!=null)
        {
            $callable = $this->isDisabled;
            return $callable($choice, $index);
        }
        return false;
    }
    
    protected final function valueForChoice($choice, $value, $index)
    {
        return $this->choiceRenderer->getValue($choice, $index)==$value;
    }
    
    protected function validateModel()
    {
        if(count($this->choices)>0)
        {
            //$firstType = null;
            //if(is_object($this->choices[0]))
            //{
                //$firstType = get_class($this->choices[0]);
            //}
            //else
            //{
                $firstType = gettype($this->choices[0]);
            //}
                
            //@todo validate model against the type

            foreach($this->choices as $choice)
            {
                if(is_array($choice) && !$this->supportNestedArray())
                {
                    throw new \InvalidArgumentException('Choices array may not contain nested arrays');
                }
                else if(is_array($choice) && $this->supportNestedArray())
                {
                    foreach($choice as $option)
                    {
                        if(is_array($option))
                        {
                            throw new \InvalidArgumentException('Nested choice arrays may contain nested arrays');
                        }
                    }
                }
                //@todo this doesn't actually work as it assumes that classes will be identical but the full hierarchy should be checked
                /*else if((is_object($choice) && get_class($choice)!=$firstType) || (!is_object($choice) && gettype($choice)!=$firstType))
                {
                    throw new \InvalidArgumentException('Choice array does not contain the same values');
                }*/
            }
        }
    }
    
    public function getValue()
    {
        $input = null;
        if($this->isDisabled())
        {
            $input = $this->getModelObject();
        }
        if($this->getRawInput()==null)
        {
            if($this->getEmptyInput()==true)
            {
                return null;
            }
            else
            {
                $input = $this->getModelObject();
            }
        }
        else
        {
            $input = $this->getRawInput();
        }
        return $input;
    }
    
    protected function supportNestedArray()
    {
        return false;
    }
}

?>
