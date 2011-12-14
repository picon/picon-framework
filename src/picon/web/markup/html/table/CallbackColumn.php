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
 * Description of CallbackColumn
 * 
 * @author Martin Cassidy
 */
class CallbackColumn extends AbstractColumn
{
    private $callback;
    
    public function __construct($heading, $callback)
    {
        Args::callBackArgs($callback, 2, 'callback');
        $this->callback = $callback;
    }
    
    public function populateCell(GridItem $item, $componentId, Model $model)
    {
        $callable = $this->callback;
        $component = $callable($componentId, $model);
        if($component==null || !($component instanceof Component) || $component->getId()!=$componentId)
        {
            throw new \IllegalStateException('Callback for CallbackColumn is expected to return a componet with the given id');
        }
        $item->add($component);
    }
   
}

?>
