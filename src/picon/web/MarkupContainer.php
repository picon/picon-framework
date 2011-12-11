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
            return;
        }
        parent::add($object);
    }
    
    public function addOrReplace(Component &$component)
    {
        if($component->added)
        {
            throw new \RuntimeException(sprintf("Component %s has been added already.", $component->getId()));
        }
        if($this->childExists($component->getId()))
        {
            $old = &$this->children;
            $old[$component->getId()] = $component;
            $this->onComponentAdded($component);
        }
        else
        {
            $this->addComponent($component);
        }
    }
    
    protected final function addComponent(Component &$component)
    {
        if($this->childExists($component->getId()))
        {
            throw new \RuntimeException(sprintf("Component %s already has a child with id %s", $this->getId(), $component->getId()));
        }
        
        if($component->added)
        {
            throw new \RuntimeException(sprintf("Component %s has already been added to another",$component->getId()));
        }
        
        if($component==$this)
        {
            throw new \RuntimeException(sprintf("Component %s cannot be added to itself.",$component->getId()));
        }
        
        $this->children[$component->getId()] = $component;
        
        $this->onComponentAdded($component);
    }
    
    public function internalInitialize()
    {
        parent::internalInitialize();
        $callback = function(&$component)
        {
            $component->internalInitialize();
            return new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL);
        };
        $this->visitChildren(Component::getIdentifier(), $callback);
    }
    
    public function internalBeforeRender()
    {
        parent::internalBeforeRender();
        foreach($this->children as $child)
        {
            $child->internalBeforeRender();
        }
    }
    
    protected function onComponentAdded(&$component)
    {
        $component->setParent($this);
        $page = $this->getPage();
        
        if($page!=null)
        {
            if(!$page->isInitialized())
            {
                $page->internalInitialize();
            }
        }
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     *
     * @param ComponentTag $tag 
     */
    protected function onComponentTagBody(ComponentTag $tag)
    {
        parent::onComponentTagBody($tag);
        if($tag->hasChildren())
        {
            $this->renderAll($tag->getChildren());
        }
    }
    
    public function get($id)
    {
        if(empty($id))
        {
            return $this;
        }
        if(substr($id, strlen($id)-1, strlen($id))!=self::PATH_SEPERATOR)
        {
            $id = $id.self::PATH_SEPERATOR;
        }
        $nodes = explode(self::PATH_SEPERATOR, $id);
        $child = $nodes[0];
        
        if($this->childExists($child))
        {
            $childComponent = $this->children[$child];
            return $childComponent->get(str_replace($child.self::PATH_SEPERATOR, '', $id));
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
     * Recursivly visit all child components matching the Identifier and execute
     * a callback on each
     * @param Identifier $identifier
     * @param closure $callback 
     */
    public function visitChildren(Identifier $identifier, $callback)
    {
        Args::callBackArgs($callback, 1);
        $this->internalVisitChildren($identifier, $this->getChildren(), $callback);
    }
    
    private function internalVisitChildren(Identifier $identifier, $components, $callback)
    {
        foreach($components as $component)
        {
            $response = new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL);
            if($component::getIdentifier()->of($identifier))
            {
                $response = $callback($component);
                Args::checkInstanceNotNull($response, VisitorResponse::getIdentifier());
            }
            if($response->equals(new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL)) && $component instanceof MarkupContainer)
            {
                $this->internalVisitChildren($identifier, $component->getChildren(), $callback);
            }
            else if($response->equals(new VisitorResponse(VisitorResponse::STOP_TRAVERSAL)))
            {
                break;
            }
        }
    }
    
    /**
     * Loads the markup accosiated with this markup container
     * @todo the functionality of this method should be extracted into
     * a helper class as it needs extending to load from parent folders
     */
    public function loadAssociatedMarkup()
    {
        return MarkupLoader::get()->loadMarkup($this);
    }
    
    protected function onRender()
    {
        $this->internalRenderComponent();
    }
    
    /**
     * Locates the ComponentTag for a particular Component
     * @param Component The child component to find the ComponentTag for
     */
    protected function getMarkupForChild(Component $child)
    {
        return $this->getMarkUpSource()->getMarkup($this, $child);
    }
}

?>
