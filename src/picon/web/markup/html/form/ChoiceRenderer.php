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
 * Description of ChoiceRenderer
 * 
 * @author Martin Cassidy
 */
class ChoiceRenderer
{
    private $valueCallable;
    private $displayCallable;
    
    /**
     *
     * @param closure $valueCallable
     * @param closure $displayCallable 
     */
    public function __construct($valueCallable = null, $displayCallable = null)
    {
        if($valueCallable!=null)
        {
            Args::callBackArgs($valueCallable, 2, 'valueCallable');
        }
        if($displayCallable!=null)
        {
            Args::callBackArgs($displayCallable, 2, 'displayCallable');
        }
        $this->displayCallable = $displayCallable;
        $this->valueCallable = $valueCallable;
    }
    
    public function getValue($choice, $index)
    {
        if($this->valueCallable==null)
        {
            return $choice;
        }
        $callable = $this->valueCallable;
        return $callable($choice, $index);
    }
    
    public function getDisplay($choice, $index)
    {
        if($this->displayCallable==null)
        {
            return $choice;
        }
        $callable = $this->displayCallable;
        return $callable($choice, $index);
    }
}

?>
