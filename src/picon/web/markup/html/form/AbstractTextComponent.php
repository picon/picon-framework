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
 * Description of AbstractTextComponent
 * 
 * @author Martin Cassidy
 */
abstract class AbstractTextComponent extends FormComponent
{
    /**
     * Converts the input into the type it is meant to be
     */
    public function convertInput()
    {
        $string = $this->getRawInput();
        $supported = array("boolean", "integer", "double", "float", "string", );
        $required = $this->getType();
        if(in_array($required, $supported))
        {
            $converted = $string;
            settype($converted, $required);
            return $converted;
        }
        else if(class_exists($required))
        {
            $converter = $this->getApplication()->getConverter(get_class($required));
            
            if($converter==null)
            {
                throw new \RuntimeException(sprintf("Unable to find converter for type %s", $required));
            }
            $object = $converter->convertToObject($string);
            if(get_class($object)!=$required)
            {
                throw new \RuntimeException("Converter did not correctly convert to an object");
            }
            return $converted;
        }
        else
        {
            throw new \InvalidArgumentException('Input could not be converted');
        }
    }
    
    /**
     * Get the data type for this text component
     */
    protected abstract function getType();
    
    /**
     * Get the value for the form component. This may come from the model
     * or, if an input is set, from the input
     * @return string the value for this component
     */
    public function getValue()
    {
        $input = $this->getRawInput();
        if(empty($input))
        {
            $input = $this->getModelObjectAsString();
        }
        return htmlentities($input);
    }
    
    public function processInput()
    {
        try
        {
            $converted = $this->convertInput();
            $this->updateModel($converted);
        }
        catch(ConversionException $ex)
        {
            //@todo show error message
        }
    }
}

?>
