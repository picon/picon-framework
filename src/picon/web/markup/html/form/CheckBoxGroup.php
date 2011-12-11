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
 * Description of CheckBoxGroup
 * 
 * @author Martin Cassidy
 */
class CheckBoxGroup extends FormComponent
{
    private $currentValue = array();
    
    protected function onInitialize()
    {
        parent::onInitialize();
        $this->validateModel();
    }
    
    private function validateModel()
    {
        $object = $this->getModelObject();
        if($object!=null && !is_array($object))
        {
            throw new \IllegalStateException('Check box group must have an array model');
        }
    }
    
    public function getRawInputArray()
    {
        $input = $this->getRawInput();
        if($input==null)
        {
            return array();
        }
        if(!is_array($input))
        {
            throw new \InvalidArgumentException('CheckBoxGroup expected raw input to be an array');
        }
        return $input;
    }
    
    public function processInput()
    { 
        $checks = array();
        $callback = function(&$component) use(&$checks)
        {
            array_push($checks, $component);
            return new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL);
        };
        $this->visitChildren(CheckBox::getIdentifier(), $callback);
        $values = array();
        
        foreach($checks as $check)
        { 
            if(in_array($check->getValue(), $this->getRawInputArray()))
            { 
                array_push($values, $check->getModelObject());
            }
        }
        $this->currentValue = $values;
        $this->updateModel($values);
    }
    
    public function getCurrentValue()
    {
        return $this->currentValue;
    }
}

?>
