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

use picon\core\Args;
use picon\web\ajax\markup\html\ModalWindow;
use picon\web\Component;
use picon\web\FormComponent;
use picon\web\markup\html\Form;

/**
 * Description of AjaxFormComponentUpdateBehavior
 * 
 * @author Martin Cassidy
 */
class AjaxFormComponentUpdateBehavior extends AjaxEventBehaviour
{
    
    public function __construct($event, $onEvent, $onSubmit, $onError)
    {
        Args::callBackArgs($onEvent, 1, 'onEvent');
        
        if($onSubmit!=null)
        {
            Args::callBackArgs($onSubmit, 1, 'onSubmit');
        }
        if($onError!=null)
        {
            Args::callBackArgs($onError, 1, 'onError');
        }
        
        $self = $this;
        parent::__construct($event, function($target) use ($self, $onEvent, $onSubmit, $onError)
        {
            $formComponent = $self->getComponent();
            $formComponent->processInput();
            $onEvent($target);
            
            if($formComponent->isValid() && $onSubmit!=null)
            {
                $onSubmit($target);
            }
            if(!$formComponent->isValid() && $onError!=null)
            {
                $onError($target);
            }
        });
    }
    
    public function bind(Component &$component)
    {
        if(!($component instanceof FormComponent))
        {
            throw new \InvalidArgumentException('AjaxFormComponentUpdateBehavior can only be added to a form component');
        }
        parent::bind($component);
    }
    
    protected function generateCallScript($url)
    {
        return sprintf("piconAjaxSubmit('%s', '%s'", $this->getSubmitForm()->getMarkupId(), $url);
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
}

?>
