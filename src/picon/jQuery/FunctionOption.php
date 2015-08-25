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

namespace picon\jquery;

use picon\core\Args;

/**
 * Produces an option which is a function
 *
 * @author Martin Cassidy
 * @package web/jquery
 */
class FunctionOption extends AbstractOption
{
    private $function;
    private $args = array();
    
    /**
     *
     * @param string $name
     * @param string $function
     * @param ... args the names of the arguments the javascript function should take
     */
    public function __construct($name, $function)
    {
        parent::__construct($name);
        Args::isString($function, 'function');
        $this->function = $function;
        
        $args = func_get_args();
        
        for($i=2;$i<count($args);$i++)
        {
            array_push($this->args, $args[$i]);
        }
    }
    
    public function render(AbstractJQueryBehaviour $behaviour)
    {
        return sprintf("%s : function(%s) {%s}", $this->getName(), implode(', ', $this->args), $this->function);
    }
}

?>
