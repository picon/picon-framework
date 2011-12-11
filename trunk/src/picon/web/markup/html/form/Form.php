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
 * Description of Form
 * 
 * @author Martin Cassidy
 */
class Form extends MarkupContainer implements FormSubmitListener
{
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->put('action', $this->urlForListener($this));
        $tag->put('method', 'post');
    }
    
    /**
     * Called when the form is submited
     * @todo get this to find the submitting component and invoke its
     * onEvent too
     */
    public function onEvent()
    {
        if($this->getRequest()->isPost())
        {
            $components = array();
            $form = $this;
            $callback = function(&$component) use (&$components, $form)
            {
                if($component->getForm()!=$form)
                {
                    return new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL_NO_DEEPER);
                }
                array_push($components, $component);
                return new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL);
            };
            $this->visitChildren(FormComponent::getIdentifier(), $callback);
            
            $formValid = true;
            foreach($components as $formComponent)
            {
                $formComponentValid = $formComponent->validate();
                if($formComponentValid==false)
                {
                    $formValid = false;
                }
            }
            
            if($formValid)
            {
                foreach($components as $formComponent)
                {
                    $formComponent->processInput();
                }
            }
            
            //hardcoded!
            $callback = function(&$component)
            {
                   $component->onEvent();
                   return new VisitorResponse(VisitorResponse::STOP_TRAVERSAL);
            };
            $this->visitChildren(Button::getIdentifier(), $callback);
        }
    }
}

?>
