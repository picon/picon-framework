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
 * Validates a number is not greater than a max value
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form/validation
 */
class MaximumValidator extends NumericValidator
{
    private $maximum;
    
    /**
     *
     * @param number $maximum 
     */
    public function __construct($maximum)
    {
        Args::isNumeric($maximum, 'maximum');
        $this->maximum = $maximum;
    }
    
    public function validateValue(Validatable $validateable)
    {
        $response = parent::validateValue($validateable);
        if($response!=null)
        {
            return $response;
        }
        if($validateable->getValue()>$this->maximum)
        {
            $response = new ValidationResponse($this->getKeyName());
            $response->addValue('max', $this->maximum);
            return $response;
        }
    }
}

?>
