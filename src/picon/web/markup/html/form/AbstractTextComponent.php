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

/**
 * A form component which can accept text based input
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
abstract class AbstractTextComponent extends FormComponent
{
    protected function convertInput()
    {
        $primatives = array(self::TYPE_BOOL, self::TYPE_DOUBLE, self::TYPE_FLOAT, self::TYPE_INT);
        $type = $this->getType();
        
        if($type==self::TYPE_STRING)
        {
            $this->setConvertedInput($this->getRawInput());
        }
        else if(in_array($type, $primatives))
        {
            $convertedInput = $this->getRawInput();
            settype($convertedInput, $type);
            $this->setConvertedInput($convertedInput);
        }
        else
        {
            try
            {
                $converter = $this->getApplication()->getConverter($type);
                if($converter==null)
                {
                    throw new ConversionException(sprintf("A converter for type %s could not be located.", $type));
                }
                else
                {
                    $converted = $converter->convertToObject($string);
                    $this->setConvertedInput($converted);
                }
            }
            catch(ConversionException $ex)
            {
                $this->invalid();
                //@todo dont hardcode error, temp message for now
                $this->error('conversion error');
            }
        }
    }
    
    protected abstract function getType();
    
    protected function validateModel()
    {
        $modelObject = $this->getModelObject();

        if($modelObject!=null)
        {
            if(is_object($modelObject) && get_class($modelObject)!=$this->getType())
            {
                throw new \IllegalStateException(sprintf("This text component needs a %s model, actual %s", $this->getType(), gettype($this->getModelObject())));
            }
            else if(!is_object($modelObject) && gettype($modelObject)!=$this->getType())
            {
                throw new \IllegalStateException(sprintf("This text component needs a %s model, actual %s", $this->getType(), gettype($this->getModelObject())));
            }
        }
    }
}

?>
