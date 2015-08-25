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

use picon\web\markup\html\form\validation\Validatable;
use picon\web\markup\html\form\validation\ValidationResponse;
use picon\web\model\BasicModel;

/**
 * A wrapper for form components for usage by validators. This is a proxy/
 * adaptor which enables a form validator to work indirectly with a form
 * component to report validation errors
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class ValidatableFormComponentWrapper implements Validatable
{
    private $wrappedComponet;
    
    public function __construct(FormComponent &$component)
    {
        $this->wrappedComponet = $component;
    }

    /**
     * @param ValidationResponse $error
     */
    public function error(ValidationResponse $error)
    {
        $error->setName($this->wrappedComponet->getId());
        $error->setValue($this->getValue());
        $message = $this->wrappedComponet->getLocalizer()->getString($this->wrappedComponet->getComponentKey($error->getKey()), new BasicModel($error));
        $this->wrappedComponet->error($message);
        $this->wrappedComponet->invalid();
    }
    
    public function getValue()
    {
        return $this->wrappedComponet->getConvertedInput();
    }
    
    public function isValid()
    {
        return $this->wrappedComponet->isValid();
    }
}

?>
