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
 * Validats that a string matches a given regular expression
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form/validation
 */
class PatternValidator extends StringValidator
{
    private $pattern;
    
    /**
     *
     * @param string $pattern The regular expression
     */
    public function __construct($pattern)
    {
        Args::isString($pattern, 'pattern');
        $this->pattern = $pattern;
    }
    
    public function validateValue(Validatable $validateable)
    {
        $response = parent::validateValue($validateable);
        if($response!=null)
        {
            return $response;
        }
        if(preg_match("/".$this->pattern."/", $validateable->getValue())!=1)
        {
            $response = new ValidationResponse($this->getKeyName(), $validateable->getValue());
            $response->addValue('expression', $this->pattern);
            return $response;
        }
    }
}

?>
