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
 * Description of CheckBox
 * 
 * @author Martin Cassidy
 */
class CheckBox extends FormComponent
{
    private $group;
    private $noParentGroup;
    private $state;
    
    protected function onInitialize()
    {
        parent::onInitialize();
        $this->validateModel();
    }
    
    private function validateModel()
    {
        $group = $this->getGroup();
        
        if($group==null && $this->getModel()!=null && !($this->getModel() instanceof BooleanModel) && !is_bool($this->getModelObject()))
        {
            throw new \IllegalStateException("A check box which is not part of a check box group must have a bollean model");
        }
    }
    
    private function getGroup()
    {
        if($this->group!=null)
        {
            return $this->group;
        }
        if($this->noParentGroup==true)
        {
            return null;
        }
        
        $group = null;
        
        $callback = function(Component &$component) use (&$group)
        {
            $group = $component;
            return new VisitorResponse(VisitorResponse::STOP_TRAVERSAL);
        };
        $this->visitParents(CheckBoxGroup::getIdentifier(), $callback);
        
        if($group==null)
        {
            $this->noParentGroup= true;
            return null;
        }
        
        $this->group = $group;
        
        return $group;
    }
    
    public function getValue()
    {
        return $this->getComponentPath();
    }
    
    private function getName()
    {
        $group = $this->getGroup();
        if($group==null)
        {
            return $this->getMarkupId();
        }
        else
        {
            return $group->getMarkupId().'[]';
        }
    }
    
    public function isSelected()
    {
        $this->validateModel();
        $group = $this->getGroup();
        if($group == null)
        {
            if(isset($this->state))
            {
                return $this->state;
            }
            else
            {
                $value = $this->getModelObject();
                settype($value, 'boolean');
                return $value;
            }
        }
        else
        {
            $inputArray = $group->getCurrentValue();
            if(count($inputArray)==0)
            {
                $inputArray = $group->getModelObject();
                
            }
            if(count($inputArray)==0)
            {
                $inputArray = array();
            }
            return in_array($this->getModelObjectAsString(), $inputArray);
        }
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag); 
        $this->checkComponentTag($tag, 'input');
        $this->checkComponentTagAttribute($tag, 'type', 'checkbox');
        $tag->put('name', $this->getName());
        $tag->put('value', $this->getValue());
        
        if($this->isSelected())
        {
            $tag->put('checked', 'checked');
        }
    }
    
    public function processInput()
    {
        $group = $this->getGroup();
        
        if($group==null)
        {
            $value = ($this->getRawInput()==$this->getValue())==true;
            $this->state = $value;
            $this->updateModel($value);
        }
        else
        {
            //Do nothing, the group will handle everything
        }
    }
    
    public function getLabel()
    {
        return $this->getModelObjectAsString();
    }
}

?>
