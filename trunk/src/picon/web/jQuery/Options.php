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
 * A set of jQuery options for use with a jQuery behaviour
 *
 * @author Martin Cassidy
 * @package web/jQuery
 */
class Options
{
    private $options = array();
    
    public function render(AbstractJQueryBehaviour $behaviour)
    {
        $out = '{';
        $total = count($this->options);
        $index = 0;
        foreach($this->options as $option)
        {
            $out .= $option->render($behaviour);
            
            if($index!=$total-1)
            {
                $out .= ',';
            }
            $index++;
        }
        $out .= '}';
        return $out;
    }
    
    public function getOption($name)
    {
        if(array_key_exists($name, $this->options))
        {
            return $this->options[$name];
        }
        else
        {
            return null;
        }
    }
    
    public function add(AbstractOption $option)
    {
        if(array_key_exists($option->getName(), $this->options))
        {
            throw new \InvalidArgumentException(sprintf('An option with the name %s exists already.', $option->getName()));
        }
        
        $this->options[$option->getName()] = $option;
    }
}

?>
