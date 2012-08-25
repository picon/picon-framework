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
 * A form component with pre-defined choices of which more than 1
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
abstract class AbstractMultipleChoice extends AbstractChoice
{
    public function getRawInputArray()
    {
        $input = $this->getRawInput();
        if($this->isEmptyInput() || $input==null)
        {
            return array();
        }
        if(!is_array($input))
        {
            throw new \InvalidArgumentException('CheckBoxGroup expected raw input to be an array');
        }
        return $input;
    }
    
    public function getName()
    {
        return parent::getName().'[]';
    }
    
    public function isSelected($choice, $index)
    {
        if($this->isEmptyInput())
        {
            return false;
        }
        else
        {
            $raw = $this->getRawInputArray();
            if(count($raw)!=0)
            {
                return in_array($this->getChoiceRenderer()->getValue($choice, $index), $raw);
            }
            else
            {
                return in_array($choice, $this->getModelObject());
            }
        }
    }
}

?>
