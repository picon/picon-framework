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

namespace picon\web;

use picon\Args;

/**
 * A form button, when submited the callback methods will be invoked based on the 
 * valid state of the form
 * 
 * @todo This currently needs to be a child of a Form - it should provide the option
 * of manually specifying the form to submit so the button may be placed elsewhere
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class Button extends FormComponent implements FormSubmitListener, FormSubmitter
{
    private $onSubmit;
    private $onError;
    
    /**
     *
     * @param string $id
     * @param callback $onSubmit Called when the form is valid
     * @param callback $onError Called when the form is not valid
     */
    public function __construct($id, $onSubmit = null, $onError = null)
    {
        parent::__construct($id);
        
        if($onSubmit!=null)
        {
            Args::callBack($onSubmit, 'onSubmit');
        }
        if($onError!=null)
        {
            Args::callBack($onError, 'onError');
        }
        
        $this->onSubmit = $onSubmit;
        $this->onError = $onError;
    }
    
    public function isStateless()
    {
        return false;
    }
    
    public function onSubmit()
    {
        $callable = $this->onSubmit;
        if($callable!=null)
        {
            $callable();
        }
    }
    
    public function onError()
    {
        $callable = $this->onError;
        if($callable!=null)
        {
            $callable();
        }
    }
    
    public function onEvent()
    {
        $form = $this->getForm();
        $form->process($this);
    }
    
    protected function convertInput()
    {
        //Do nothing
    }
    
    public function updateModel()
    {
        //Do nothing
    }
    
    protected function validateModel()
    {
        //Do nothing
    }
}

?>
