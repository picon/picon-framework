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
 * Description of AbstractRepeater
 * 
 * @author Martin Cassidy
 */
abstract class AbstractRepeater extends MarkupContainer
{
    public function __construct($id, $model = null)
    {
        if($model!=null && !($model instanceof ArrayModel))
        {
            throw new \InvalidArgumentException('List View must have an Array Model');
        }
        if($model==null)
        {
            parent::__construct($id);
        }
        else
        {
            parent::__construct($id, $model);
        }
    }
    
    protected function onRender()
    {
        foreach($this->getModel()->getModelObject() as $index => $object)
        {
            $entry = $this->get($this->getId().$index);
            $entry->render();
        }
    }
    
    abstract function renderIteration($entry);
    
    protected function getMarkupForChild(Component $child)
    {
        return $this->getMarkup();
    }
    
    public function beforeRender()
    {
        parent::beforeRender();
        foreach($this->getChildren() as $child)
        {
            $this->remove($child);
        }
        $this->populate();
    }
    
    protected abstract function populate();
}

?>