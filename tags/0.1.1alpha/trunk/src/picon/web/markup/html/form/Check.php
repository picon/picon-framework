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
 * A checkbox for use inside a check group
 * 
 * @see CheckGroup
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class Check extends LabeledMarkupContainer
{
    private $group;
    
    public function getLabel()
    {
        return $this->getModelObjectAsString();
    }
    
    private function getGroup()
    {
        if($this->group!=null)
        {
            return $this->group;
        }
        
        $group = null;
        
        $callback = function(Component &$component) use (&$group)
        {
            $group = $component;
            return Component::VISITOR_STOP_TRAVERSAL;
        };
        $this->visitParents(CheckBoxGroup::getIdentifier(), $callback);
        
        $this->group = $group;
        
        if($group==null)
        {
            throw new \RuntimeException('A check must be a child of CheckBoxGroup');
        }
        
        return $group;
    }
    
    public function getValue()
    {
        return $this->getComponentPath();
    }
    
    public function getName()
    {
        $group = $this->getGroup();
        return $group->getName().'[]';
    }
    
    protected function isSelected($value)
    {
        $group = $this->getGroup();
        if(count($group->getRawInputArray())==0)
        {
            if($group->isEmptyInput())
            {
                return false;
            }
            else
            {
                return in_array($this->getModelObject(), $group->getModelObject());
            }
        }
        else
        {
            return in_array($value, $group->getRawInputArray());
        }
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag); 
        $this->checkComponentTag($tag, 'input');
        $this->checkComponentTagAttribute($tag, 'type', 'checkbox');
        $tag->put('value', $this->getValue());
        $tag->put('name', $this->getName());
        
        $tag->remove('checked');
        if($this->isSelected($this->getValue()))
        {
            $tag->put('checked', 'checked');
        }
    }
}
?>
