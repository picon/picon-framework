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
 * Description of Radio
 * 
 * @author Martin Cassidy
 */
class Radio extends LabeledMarkupContainer
{
    private $group;
   
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
            return new VisitorResponse(VisitorResponse::STOP_TRAVERSAL);
        };
        $this->visitParents(RadioGroup::getIdentifier(), $callback);
        
        $this->group = $group;
        
        if($group==null)
        {
            throw new \RuntimeException('A radio must be a child of RadioGroup');
        }
        
        return $group;
    }
    
    public function getName()
    {
        return $this->getGroup()->getMarkupId();
    }

    public function getValue()
    {
        return $this->getComponentPath();
    }
    
    private function isChecked()
    {
        $group = $this->getGroup();
        
        if($group->getRawInput()!=null)
        {
            return $group->getRawInput()==$this->getValue();
        }
        else
        {
            return $group->getModelObject()==$this->getModelObject();
        }
        
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $this->checkComponentTag($tag, 'input');
        $this->checkComponentTagAttribute($tag, 'type', 'radio');
        
        $tag->put('name', $this->getName());
        $tag->put('value', $this->getValue());

        if($this->isChecked())
        {
            $tag->put('checked', 'checked');
        }
    }
    
    public function getLabel()
    {
        return $this->getModelObjectAsString();
    }
}

?>
