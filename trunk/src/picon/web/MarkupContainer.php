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
 * Description of MarkupComponent
 * 
 * @author Martin Cassidy
 * @package web
 */
class MarkupContainer extends Component
{
    private $children = array();
    
    public function add(&$object)
    {
        if($object instanceof Component)
        {
            $this->addComponent($object);
        }
        elseif($object instanceof Behaviour)
        {
            $this->addBehaviour($object);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf("Expected paramater 1 to be a Behaviour or a Component, %s given", gettype($object)));
        }
    }
    
    protected final function addComponent(Component &$component)
    {
        if($this->childExists($component->getId()))
        {
            throw new \RuntimeException(sprintf("Component %s already has a child with id %s", $this->getId(), $component->getId()));
        }
        $this->children[$component->getId()] = $component;
        $component->setParent($this);
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    public function get($id)
    {
        if($this->childExists($id))
        {
            return $this->children[$id];
        }
        return null;
    }
    
    protected function childExists($id)
    {
        return array_key_exists($id, $this->children);
    }
    
    public function hasChildren()
    {
        return count($this->children)>0;
    }
    
    /**
     * Loads the markup accosiated with this markup container
     * @todo the functionality of this method should be extracted into
     * a helper class as it needs extending to load from parent folders
     */
    protected function loadAssociatedMarkup()
    {
        $reflection = new \ReflectionClass($this);
        $fileInfo = new \SplFileInfo($reflection->getFileName());
        $parser = new MarkupParser();
        return $parser->parse($fileInfo->getPath()."\\".get_class($this).'.html');
    }
    
    protected function onRender()
    {
        $this->internalRenderComponent();
    }
}

?>
