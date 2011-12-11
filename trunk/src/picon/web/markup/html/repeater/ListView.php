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
 * Description of ListView
 * 
 * @author Martin Cassidy
 */
class ListView extends AbstractRepeater
{
    private $callback;
    
    public function __construct($id, $model = null, $callback)
    {
        parent::__construct($id, $model);
        Args::callBack($callback);
        $reflection = new \ReflectionFunction($callback);
        if($reflection->getNumberOfParameters()!=1)
        {
            throw new \InvalidArgumentException("Callback must have 1 argument");
        }
        $this->callback = $callback;
    }
    
    public function renderIteration($entry)
    {
        $callable = $this->callback;
        $callable($entry);
    }
    
    protected function populate()
    {
        foreach($this->getModel()->getModelObject() as $index => $object)
        {
            $model = $this->getModel()->getModelObject();
            $entry = new ListItem($this->getId().$index, new BasicModel($model[$index]), $index);
            $this->renderIteration($entry);
            $this->addOrReplace($entry);
        }
    }
}

?>
