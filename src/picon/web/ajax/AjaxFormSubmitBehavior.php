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

namespace picon\web\ajax;

use Closure;
use picon\core\Args;
use picon\web\ajax\markup\html\ModalWindow;
use picon\web\Component;
use picon\web\markup\html\form\Form;
use picon\web\markup\html\form\FormComponent;
use picon\web\markup\html\form\FormSubmitter;
use picon\web\request\target\AjaxRequestTarget;

/**
 * Ajax implementation that will submit a form on a javascript event
 * By default, this will be the parent form of the component that this is added to
 *
 * @author Martin Cassidy
 * @package web/ajax
 */
class AjaxFormSubmitBehavior extends AjaxEventBehaviour implements FormSubmitter
{
    private $form;
    private $onSubmit;
    private $onError;
    private $target;

    /**
     * @param $event
     * @param closure $onSubmit
     * @param closure $onError
     * @param Form $form
     */
    public function __construct($event, $onSubmit = null, $onError = null, Form $form = null)
    {
        $self = $this;
        parent::__construct($event, function($target) use ($self)
        {
            $self->setAjaxRequestTarget($target);
            $self->getForm()->process($self);
        });
        
        if($onSubmit!=null)
        {
            Args::callBackArgs($onSubmit, 1, 'onSubmit');
        }
        if($onError!=null)
        {
            Args::callBackArgs($onError, 1, 'onError');
        }
        
        $this->onSubmit = $onSubmit;
        $this->onError = $onError;
        
        if($form!=null)
        {
            if($form instanceof Form)
            {
                $this->form = $form;
            }
            else
            {
                throw new \InvalidArgumentException('$form must be an instance of Form');
            }
        }
    }
    
    public function setAjaxRequestTarget(AjaxRequestTarget $target)
    {
        $this->target = $target;
    }
    
    public function getForm()
    {
        $form = $this->form;
        $component = $this->getComponent();
        if($component instanceof FormComponent)
        {
            $form = $component->getForm();
        }
        
        if($form==null)
        {
            throw new \picon\core\exceptions\IllegalStateException(sprintf('Unable to locate form for ajax submit behaviour on component %s', $component->getId()));
        }
        return $form;
    }
    
    public function bind(Component &$component)
    {
        parent::bind($component);
        $this->getForm()->setOutputMarkupId(true);
    }
    
    private function getSubmitForm()
    {
        $usingComponent = $this->form;
        $form = $this->form;
        if($usingComponent==null)
        {
            $usingComponent = $this->getComponent();
        }
        
        $callback = function(&$component) use (&$form)
        {
            if($component instanceof Form)
            {
                $form = $component;
            }
            if($component instanceof ModalWindow)
            {
                return Component::VISITOR_STOP_TRAVERSAL;
            }
            
            return Component::VISITOR_CONTINUE_TRAVERSAL;
        };
        $usingComponent->visitParents(Component::getIdentifier(), $callback);
        return $form;
    }
    
    protected function generateCallScript($url)
    {
        return sprintf("piconAjaxSubmit('%s', '%s'", $this->getSubmitForm()->getMarkupId(), $url);
    }
    
    public function onError()
    {
        $callable = $this->onError;
        if($callable!=null)
        {
            $callable($this->target);
        }
    }
    
    public function onSubmit()
    {
        $callable = $this->onSubmit;
        if($callable!=null)
        {
            $callable($this->target);
        }
    }
}

?>
