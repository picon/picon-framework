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

namespace picon\web\ajax\markup\html;

use picon\web\ajax\AjaxCallDecorator;
use picon\web\ajax\AjaxFormSubmitBehavior;
use picon\web\ajax\CallDecoratorWrapper;
use picon\web\Button;
use picon\web\ComponentTag;
use picon\web\markup\html\form\Form;

/**
 * A form button which, when clicked will submit the form by ajax
 * 
 * One of the calback methods (onSubmit or onError) will be invoked to
 * allow for a response to be created in either of the posible circumstances
 *
 * @author Martin Cassidy
 * @package web/ajax/markup/html
 */
class AjaxButton extends Button implements CallDecoratorWrapper
{
    private $form;
    private $behaviour;
    
    /**
     *
     * @param $id
     * @param $onSubmit
     * @param $onError
     * @param Form $form 
     */
    public function __construct($id, $onSubmit = null, $onError = null, $form = null)
    {
        parent::__construct($id);
        
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
        
        $this->behaviour = new AjaxFormSubmitBehavior('onClick', $onSubmit, $onError, $form);
        $this->add($this->behaviour);
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->put('type', 'button');
    }
    
    public function getForm()
    {
        if($this->form==null)
        {
            return parent::getForm();
        }
        else
        {
            return $this->form;
        }
    }
    
    public function setAjaxCallDecorator(AjaxCallDecorator &$decorator)
    {
        $this->behaviour->setAjaxCallDecorator($decorator);
    }
}

?>
