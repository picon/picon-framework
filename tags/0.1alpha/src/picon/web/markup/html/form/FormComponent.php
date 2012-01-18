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
 * Superclass for all form components
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
abstract class FormComponent extends LabeledMarkupContainer
{    
    /**
     * An array of Validator
     * 
     * @var array
     */
    private $validators = array();
    
    private $emptyInput = false;
    
    /**
     * @var mixed The raw input from the request 
     */
    private $rawInput;
    
    /**
     *
     * @var mixed the processed and converted input for the form component
     * @Transient
     */
    private $convertedInput;
    
    /**
     * @var boolean is this form component manditory 
     */
    private $required = false;
    
    /**
     * Add a new validator, child component or behavior to the component
     * @param mixed $object
     * @return void 
     */
    public function add(&$object)
    {
        if($object instanceof Validator)
        {
            $this->addValidator($object);
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
        $tag->put('name', $this->getName());
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
            return Component::VISITOR_STOP_TRAVERSAL;
        };
        $this->visitParents(Form::getIdentifier(), $callback);
        return $form;
    }
    
    /**
     * @return string The raw input from the request
     */
    public function getRawInput()
    {
        return $this->rawInput;
    }
    
    /**
     * 
     * @return array
     */
    public function getRawInputArray()
    {
        $input = $this->getRawInput();
        if($input==null)
        {
            return array();
        }
        if(!is_array($input))
        {
            throw new \InvalidArgumentException('CheckBoxGroup expected raw input to be an array');
        }
        return $input;
    }
    
    /**
     * {@inheritdoc}
     * @return boolean 
     */
    public function validate()
    {
        if($this->isValid())
        {
            $this->validateRequired();
        }

        if($this->isValid())
        {
            $this->convertInput();
        }
            
        if($this->isValid() && !$this->isEmptyInput())
        {
            $validatable = new ValidatableFormComponentWrapper($this);
            foreach($this->validators as $validator)
            {
                $validator->validate($validatable);
            }
        }
        
        if($this->isValid())
        {
            $this->valid();
        }
    }
    
    /**
     * Update the model object of the form component with the new
     * object value.
     * 
     * This should only be called after the conponent has been validated.
     * Do not call this method unless you know what you are doing!
     */
    public function updateModel()
    {
        $this->setModelObject($this->convertedInput);
    }
    
    /**
     * Complete a full form component process: validate,
     * if valid update the model
     */
    public final function processInput()
    {
        $this->inputChanged();
        $valid = $this->validate();
        
        if($valid)
        {
            $this->processInput();
            $this->updateModel();
        }
    }
    
    /**
     * Convert the input from its raw format the that which is expected
     * Sub classes which need to handle this themselves should override this method
     * and have getType() throw UnsupportedOperationException
     */
    protected abstract function convertInput();
    
    public function isValid()
    {
        return !$this->hasErrorMessage();
    }
    
    /**
     *
     * @param boolean $required 
     */
    public function setRequired($required)
    {
        Args::isBoolean($required, 'required');
        $this->required = $required;
    }
    
    public function isRequired()
    {
        return $this->required;
    }
    
    public function validateRequired()
    {
        if($this->isRequired() && ($this->rawInput==null || empty($this->rawInput) || (is_array($this->rawInput) && count($this->rawInput)<1)))
        {
            $data = array('name' => $this->getId());
            $message = $this->getLocalizer()->getString($this->getComponentKey('Required'), new ArrayModel($data));
            $this->error($message);
            $this->invalid();
        }
    }
    
    public function getConvertedInput()
    {
        return $this->convertedInput;
    }
    
    protected function setConvertedInput($convertedInput)
    {
        $this->convertedInput = $convertedInput;
    }
    
    public function getName()
    {
        return $this->getComponentPath();
    }
    
    /**
     * Called by Form when the form input has changed
     */
    public function inputChanged()
    {
        $this->emptyInput = false;
        
        $raw = $this->getRequest()->getPostedParameter(str_replace('[]', '', $this->getName()));
        
        if($raw!=null && !empty($raw) || is_array($raw) && count($raw)>0)
        {
            $this->rawInput = $raw;
        }
        else
        {
            $this->emptyInput = true;
            $this->rawInput = null;
        }
    }
    
    public function getValue()
    {
        $input = null;
        if($this->rawInput==null)
        {
            if($this->emptyInput==true)
            {
                return null;
            }
            else
            {
                $input = $this->getModelObjectAsString();
            }
        }
        else
        {
            $input = $this->rawInput;
        }
        return htmlentities($input);
    }
    
    public function invalid()
    {
        
    }
    
    public function valid()
    {
        
    }
    
    public function isEmptyInput()
    {
        return $this->emptyInput;
    }
    
    protected abstract function validateModel();
    
    public function beforeComponentRender()
    {
        parent::beforeComponentRender();
        $this->validateModel();
    }
}

?>
