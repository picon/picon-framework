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

use picon\core\Args;
use picon\core\Types;

/**
 * A component whose mark-up is rendered multiple times based on some form
 * of repitition
 * 
 * @author Martin Cassidy
 * @package web/markup/html/repeater
 */
abstract class AbstractRepeater extends MarkupContainer
{
    public function __construct($id, $model = null)
    {
        if($model!=null && !($model instanceof ArrayModel) && gettype($model->getModelObject())!= Types::TYPE_ARRAY)
        {
            throw new \InvalidArgumentException('List View must have an Array Model or a model of an array');
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
    
    /**
     * Sub classes should implement this method to return
     * an array of all of the child to be rendered
     */
    protected abstract function getRenderArray();
    
    protected function onRender()
    {
        $components = $this->getRenderArray();
        Args::isArray($components, 'getRenderArray return value');
        foreach($components as $index => $component)
        {
            $this->renderChild($component);
        }
    }
    
    protected function renderChild(Component $child)
    {
        $child->render();
    }
    
    public function getMarkupForChild(Component $child)
    {
        return $this->getMarkup();
    }
    
    public function beforePageRender()
    {
        parent::beforePageRender();
        $this->populate();
    }
    
    protected abstract function populate();
}

?>
