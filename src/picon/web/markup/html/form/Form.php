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

namespace picon\web\markup\html\form;

use picon\core\Args;
use picon\web\AttributeModifier;
use picon\web\BasicModel;
use picon\web\Button;
use picon\web\Component;
use picon\web\ComponentTag;
use picon\web\FormComponent;
use picon\web\FormSubmitListener;
use picon\web\FormSubmitter;
use picon\web\MarkupContainer;

/**
 * A form which will contain form component.
 * This will process and validate the post data when the callback is invoked.
 * If a form is submitted with a button, the appropriate button callback will also
 * be invoked
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class Form extends MarkupContainer implements FormSubmitListener
{
    private $onSubmit;
    private $onError;
    
    public function __construct($id, $model = null, $onSubmit = null, $onError = null)
    {
        parent::__construct($id, $model);
        if($onError!=null)
        {
            Args::callBack($onError, 'onError');
            $this->onError = $onError;
        }
        if($onSubmit!=null)
        {
            Args::callBack($onSubmit, 'onSubmit');
            $this->onSubmit = $onSubmit;
        }
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $this->checkComponentTag($tag, 'form');
        $tag->put('action', $this->urlForListener($this));
        $tag->put('method', 'post');
    }
    
    /**
     * Called when the form is submited
     * @todo sort out code for nested forms
     */
    public function onEvent()
    {
        $formToProcess = $this;
     
        $submitter = $this->getSubmitingButton();
        
        if($submitter!=null)
        {
            $formToProcess = $submitter->getForm();
        }
        $formToProcess->process($submitter);
    }
    
    public function process(FormSubmitter $submitter)
    {
        if($this->getRequest()->isPost())
        {
            $components = array();
            $form = $this;
            $callback = function(&$component) use (&$components, $form)
            {
                if($component->getForm()!=$form)
                {
                    return Component::VISITOR_CONTINUE_TRAVERSAL_NO_DEEPER;
                }
                array_push($components, $component);
                return Component::VISITOR_CONTINUE_TRAVERSAL;
            };
            $this->visitChildren(FormComponent::getIdentifier(), $callback);

            foreach($components as $formComponent)
            {
                $formComponent->inputChanged();
            }
            
            $formValid = true;
            foreach($components as $formComponent)
            {
                $formComponent->validate();
                if(!$formComponent->isValid())
                {
                    $formValid = false;
                }
            }
            
            if($formValid)
            {
                foreach($components as $formComponent)
                {
                    $formComponent->updateModel();
                }
                $this->callSubmit($submitter);
            }
            else
            {
                $this->callOnError($submitter);
            }
        }
    }
    
    public function isFormValid()
    {
        $valid = true;
        $callback = function(FormComponent &$component) use (&$valid)
        {
            if(!$component->isValid())
            {
                $valid = false;
                return Component::VISITOR_STOP_TRAVERSAL;
            }
            return Component::VISITOR_CONTINUE_TRAVERSAL;
        };
        $this->visitChildren(FormComponent::getIdentifier(), $callback);
        return $valid;
    }
    
    public function getSubmitingButton()
    {
        $button = null;
        $request = $this->getRequest();
        $callback = function(Button &$component) use (&$button, $request)
        {
            $value = $request->getPostedParameter($component->getName());
            if($value!=null)
            {
                $button = $component;
                return Component::VISITOR_STOP_TRAVERSAL;
            }
            return Component::VISITOR_CONTINUE_TRAVERSAL;
        };
        $this->visitChildren(Button::getIdentifier(), $callback);
        return $button;
    }
    
    private function callOnError($submiter)
    {
        $this->onError();
        if($submiter!=null && $submiter instanceof FormSubmitter)
        {
            $submiter->onError();
        }
    }
    
    private function callSubmit($submiter)
    {
        $this->onSubmit();
        if($submiter!=null && $submiter instanceof FormSubmitter)
        {
            $submiter->onSubmit();
        }
    }
    
    public function onError()
    {
        if(is_callable($this->onError))
        {
            $callable = $this->onError;
            $callable();
        }
    }
    
    public function onSubmit()
    {
        if(is_callable($this->onSubmit))
        {
            $callable = $this->onSubmit;
            $callable();
        }
    }
    
    public function beforeComponentRender()
    {
        parent::beforeComponentRender();
        $multipart = false;
        $callback = function(&$component) use (&$multipart)
        {
            if($component->isMultiPart())
            {
                $multipart = true;
            }
            return Component::VISITOR_CONTINUE_TRAVERSAL;
        };
        $this->visitChildren(FormComponent::getIdentifier(), $callback);
        
        if($multipart)
        {
            $this->add(new AttributeModifier('enctype', new BasicModel('multipart/form-data')));
        }
    }
}

?>
