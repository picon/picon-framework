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
 * Component sersvices as the hightest and most abstract super class for all
 * components. 
 * 
 * Component supports complex serialisation @see PiconSerializer
 * Component automatically injects resources on instantiation @see Injector
 * 
 * A component must have a unique ID that is passed in the constructor. The ID 
 * need only be unique amoung sibling component in the component hierarchy.
 * 
 * Components are organised into a simple hierachy. With the exception of the 
 * ultimate parent at the top of the hierarchy, a component will always have a parent.
 * 
 * If the component is an instance of MarkupContainer it can have children added to it.
 * 
 * @author Martin Cassidy
 * @package web
 * @todo Get rid of all the echo's in here
 */
abstract class Component extends PiconSerializer implements InjectOnWakeup
{
    /**
     * @var String the ID of this component
     */
    private $id;
    
    /**
     * @var Component the parent of this component in the hierarchy
     */
    private $parent;
    
    /**
     *
     * @var Array The behaviours that have been added to this component
     */
    private $behaviours = array();
    
    /**
     *
     * @var mixed MarkupElement or array of MarkupElements. The markup associated with this component
     */
    private $markup;
    
    /**
     * Create a new component. Any overrides of the constructor must call the super.
     * @param String the ID of this component
     */
    public function __construct($id)
    {
        $this->id = $id;
        Injector::get()->inject($this);
    }

    public function add(&$object)
    {
        if($object instanceof Behaviour)
        {
            $this->addBehaviour($object);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf("Expected paramater 1 to be a Behaviour %s given", gettype($object)));
        }
    }
    
    protected final function addBehaviour(Behaviour &$behaviour)
    {
        array_push($this->behaviours, $behaviour);
    }
    
    /**
     * Gets the markup for this component
     */
    private function getMarkup()
    {
        if($this->markup!=null)
        {
            return $this->markup;
        }
        else
        {
            if($this->parent==null)
            {
                if($this instanceof MarkupContainer)
                {
                    return $this->loadAssociatedMarkup();
                }
                else
                {
                    throw new \MarkupNotFoundException(sprintf("Component %s has no associated markup and no parent to get markup from", $this->id));
                }
                
            }
            else
            {
                return $this->parent->getMarkupForChild($this);
            }
        }
    }
    
    /**
     * Locates the ComponentTag for a particular Component
     * @param Component The child component to find the ComponentTag for
     */
    private function getMarkupForChild(Component $child)
    {
        $markup = $this->getMarkup();
        $componentTag = MarkupUtils::findComponentTag($markup, $child->id);
        
        if($componentTag==null)
        {
            throw new \RuntimeException(sprintf("Markup element for component id %s not found. This means you have referenced the component in your code but not your markup.", $child->id));
        }
        return $componentTag;
    }
    
    public function beforeRender()
    {
        /*
         * This does nothing by default and is for sub classes to customize if needed
         */
    }
    
    /**
     * @todo call before render for behaviours
     */
    private function internalBeforeRender()
    {
        $this->beforeRender();
        
        if($this instanceof MarkupContainer)
        {
            foreach($this->getChildren() as $child)
            {
                $child->internalBeforeRender();
            }
        }
    }
    
    /**
     * @todo call after render for behaviours
     */
    private function internalAfterRender()
    {
        $this->afterRender();
        
        if($this instanceof MarkupContainer)
        {
            foreach($this->getChildren() as $child)
            {
                $child->internalAfterRender();
            }
        }
    }
    
    public function afterRender()
    {
        /*
         * This does nothing by default and is for sub classes to customize if needed
         */
    }
    
    public function render()
    {
        $exception = null;
        
        try
        {
            $this->internalBeforeRender();
            $this->internalRender();
        }
        catch(Exception $ex)
        {
            $exception = $ex;
        }
        try
        {
            $this->internalAfterRender();
        }
        catch(Exception $ex)
        {
             if($exception==null)
             {
                $exception = $ex;
             }
        }
        
        if($exception!=null)
        {
            throw $exception;
        }
    }
    
    private function internalRender()
    {
        $markup = $this->getMarkup();
        
        if($markup==null)
        {
            throw new \MarkupNotFoundException(sprintf("Markup not found for component %s.", $this->id));
        }
        $this->onRender();
    }
    
    /**Renders this component
     * 
     */
    protected final function internalRenderComponent()
    {
        $markup = $this->getMarkup();
        
        if($markup==null)
        {
            throw new \MarkupNotFoundException(sprintf("Markup not found for component %s.", $this->id));
        }
        
        if(!($markup instanceof ComponentTag) || $markup->getComponentTagId()!=$this->id)
        {
            throw new \MarkupNotFoundException(sprintf("The correct markup for component %s was not found.", $this->id));
        }
        
        $this->onComponentTag($markup);
        $this->renderElement($markup);
    }
    
    /**
     * Renders the start tag for the passed element
     * @param MarkupElement The markup element to be rendered
     */
    private final function renderElementStart(MarkupElement $element)
    {
        echo '<'.$element->getName();
        echo $this->renderAttributes($element->getAttributes());
        if($element->isOpenClose())
        {
            echo ' /';
        }
        echo '>';
    }
    
    /**
     * Renders the close tag (if needed) for the element
     * @param MarkupElement The markup element to be rendered 
     */
    private final function renderElementEnd(MarkupElement $element)
    {        
        if($element->isOpen())
        {
            echo '</'.$element->getName().'>';
        }
    }
    
    /**
     * Renders the attributes
     * @param Array the array of attributes
     */
    private function renderAttributes($attributes)
    {
        foreach($attributes as $name => $value)
        {
            echo ' '.$name.'="'.$value.'"';
        }
    }
    
    private function renderElement(MarkupElement $element)
    {
        $this->renderElementStart($element);
        if($element->hasChildren())
        {
            $this->renderAll($element->getChildren());
        }
        $this->renderElementEnd($element);
    }
    
    /**
     * Render all of the markup elements in the array
     * @param Array An array of markup elements
     */
    protected function renderAll($markup = null)
    {
        if($markup==null)
        {
            $markup = $this->getMarkup();

            if($markup==null)
            {
                throw new \MarkupNotFoundException(sprintf("Markup not found for component %s.", $this->id));
            }
        }

        foreach($markup as $element)
        {
            if($element instanceof ComponentTag)
            {
                if($this instanceof MarkupContainer)
                {
                    $child = $this->get($element->getComponentTagId());
                    if($child!=null)
                    {
                        $child->render();
                    }
                    else
                    {
                        throw new \RuntimeException(sprintf("A component was not found for element with picon:id %s. This may be because you have forgotten to create it in your code or the hierarchy is wrong", $element->getComponentTagId()));
                    }
                }
                else
                {
                    throw new \InvalidMarkupException(sprintf("Markup element %s may not contain a child with a picon:id as the component %s cannot not have any child components", $element->getName(), $this->id));
                }
            }
            elseif($element instanceof PiconTag)
            {
                //@todo
            }
            elseif($element instanceof StringElement)
            {
                echo $element->getCharacterData();
            }
            else
            {
                $this->renderElement($element);
            }
        }
    }
    
    protected abstract function onRender();
    
    /**
     * This is called imediatly before the tag is written to the output
     * This method allows direct manipulation of the object representing the 
     * actual markup element that is to be rendered.
     * 
     * When overriding this method you must remember to call the super.
     * @param ComponentTag The tag being rendered
     */
    protected function onComponentTag(ComponentTag $tag)
    {
        if($this instanceof MarkupContainer && $this->hasChildren())
        {
            $tag->setTagType(new XmlTagType(XmlTagType::OPEN));
        }
        //@todo call onComponentTag for behaviours
    }
    
    protected function onComponentTagBody(ComponentTag $tag)
    {
        
    }
    
    protected function setParent($parent)
    {
        $this->parent = $parent;
    }
    
    protected final function getApplication()
    {
        return $GLOBALS['application'];
    }
    
    protected final function getRequestCycle()
    {
        return $GLOBALS['requestCycle'];
    }
    
    protected final function getRequest()
    {
        return $this->getRequestCycle()->getRequest();
    }
    
    protected final function getResponse()
    {
        return $this->getRequestCycle()->getResponse();
    }
    
    public function getId()
    {
        return $this->id;
    }
}

?>
