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
 * Description of FormComponent
 * 
 * @author Martin Cassidy
 */
abstract class FormComponent extends LabeledMarkupContainer implements Validatable
{
    /**
     * An array of Validator
     * 
     * @var array
     */
    private $validators = array();
    
    /**
     * @var mixed 
     * @Transient
     */
    private $rawInput;
    
    /**
     * Add a new validator, child component or behavior to the component
     * @param mixed $object
     * @return void 
     */
    public function add(&$object)
    {
        if($object instanceof Validator)
        {
            $this->add($object);
            return;
        }
        parent::add($object);
    }
    
    /**
     * Add a new validator to the component
     * @param Validator $validator 
     */
    public function addValidator(Validator &$validator)
    {
        array_push($this->validators, $validator);
    }
    
    /**
     * @todo
     * @return type 
     */
    public function getLabel()
    {
        return "";
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $tag->put('name', $this->getMarkupId());
    }
    
    /**
     * Find the form in which this component is located
     * @return Form The form to which this component belongs 
     */
    public function getForm()
    {
        $form;
        $callback = function($component) use (&$form)
        {
            $form = $component;
            return new VisitorResponse(VisitorResponse::STOP_TRAVERSAL);
        };
        $this->visitParents(Form::getIdentifier(), $callback);
        return $form;
    }
    
    /**
     * @return string The raw input from the request
     */
    public function getRawInput()
    {
        if(!isset($this->rawInput))
        {
            $this->rawInput = $this->getRequest()->getPostedParameter($this->getMarkupId());
        }
        return $this->rawInput;
    }
    
    /**
     * {@inheritdoc}
     * @return boolean 
     */
    public function validate()
    {
        $input = $this->getRawInput();
        $valid = true;
        foreach($this->validators as $validator)
        {
            $validatorValid = $validator->validate($input);
            if($validatorValid==false)
            {
                $valid = false;
            }
        }
        return $valid;
    }
    
    /**
     * Update the model object of the form component with the new
     * object value.
     * 
     * This should only be called after the conponent has been validated.
     * Do not call this method unless you know what you are doing!
     * @param mixed $newValue The new object value
     */
    public function updateModel($newValue)
    {
        $this->setModelObject($newValue);
    }
    
    /**
     * Complete a full form component process: validate,
     * if valid update the model
     */
    public final function processFormComponent()
    {
        $valid = $this->validate();
        
        if($valid)
        {
            $this->processInput();
        }
    }
    
    public abstract function processInput();
}

?>
