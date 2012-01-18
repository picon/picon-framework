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
 * A container for radio buttons
 * 
 * @see Radio
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class RadioGroup extends FormComponent
{
    public function getChoiceGroup()
    {
        $choice = null;
        $callback = function(&$component) use (&$choice)
        {
             $choice = $component;
             return Component::VISITOR_STOP_TRAVERSAL;
        };
        $this->visitParents(Identifier::forName('picon\ChoiceGroup'), $callback);
        return $choice;
    }
    
    public function getName()
    {
        $choice = $this->getChoiceGroup();
        if($choice==null)
        {
            return parent::getName();
        }
        else
        {
            return $choice->getComponentPath();
        }
    }
    
    protected function convertInput()
    {
        $input = $this->getRawInput();
        $value = null;
        $callback = function(Radio &$radio) use(&$value, $input)
        {
            if($radio->getValue()==$input)
            {
                $value = $radio->getModelObject();
                return Component::VISITOR_STOP_TRAVERSAL;
            }
            return Component::VISITOR_CONTINUE_TRAVERSAL;
        };
        $this->visitChildren(Radio::getIdentifier(), $callback);
        
        if($value!=null)
        {
            $this->setConvertedInput($value);
        }
    }
    
    public function isRequired()
    {
        $choice = $this->getChoiceGroup();
        return $choice==null && parent::isRequired();
    }
    
    protected function validateModel()
    {
        //@todo
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->remove('name');
    }
}

?>
