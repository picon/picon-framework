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
 * @todo finish adding state flags so that checks can be run to ensure overriden methods are calling
 * the parent implementation
 */
abstract class Component extends PiconSerializer implements InjectOnWakeup, Identifiable
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
     * @var Array The behaviours that have been added to this component
     */
    private $behaviours = array();
    
    /**
     * @var mixed MarkupElement or array of MarkupElements. The markup associated with this component
     */
    private $markup;
    
    /**
     * @var boolean true if this component has been rendered
     */
    private $rendered = false;
    
    private $initialized = false;
    private $flagInitializeParentCall = false;
    
    private $model;
    
    /**
     * @var boolean true if this component is in the hierarchy
     */
    protected $added = false;
    
    private $markupSource = null;
    
    const PATH_SEPERATOR = ':';
    
    /**
     * Create a new component. Any overrides of the constructor must call the super.
     * @param String the ID of this component
     */
    public function __construct($id, Model $model = null)
    {
        $this->id = $id;
        $this->model = $model;
        PiconApplication::get()->getComponentInstantiationListener()->onInstantiate($this);
    }

    /**
     * Called when the component hierarchy above this compoent is complete
     * If overriding this method you MUST call parent::onInitialize()
     */
    protected function onInitialize()
    {
        $this->flagInitializeParentCall = true;
        PiconApplication::get()->getComponentInitializationListener()->onInitialize($this);
    }
    
    protected final function fireInitialize()
    {
        if($this->isInitialized())
        { 
            return;
        }
        $this->initialized = true;
        $this->flagInitializeParentCall = false;
        $this->onInitialize();
        if(!$this->flagInitializeParentCall)
        {
            throw new \IllegalStateException(sprintf("Parent implementation of onInitialize for component %s was not called", $this->id));
        }
    }
    
    public function internalInitialize()
    {
        $this->fireInitialize();
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
    public function getMarkup()
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
     * @todo change this to a callback
     */
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
        if(!$this->isInitialized())
        {
            $this->internalInitialize();
        }
        PiconApplication::get()->getComponentBeforeRenderListener()->onBeforeRender($this);
        $this->beforeRender();
    }
    
    public function isInitialized()
    {
        return $this->initialized;
    }
    
    /**
     * @todo call after render for behaviours
     */
    private function internalAfterRender()
    {
        $this->rendered = true;
        PiconApplication::get()->getComponentAfterRenderListenersr()->onAfterRender($this);
        $this->afterRender();
        
        if($this instanceof MarkupContainer)
        {
            foreach($this->getChildren() as $child)
            {
                /**
                 * If the child component has not been rendered it must
                 * not have been present in the markup
                 * @todo move this into another non component parent calling method
                 * so that all children may have internalAfterRender
                 */
                if(!$child->rendered)
                {
                    throw new \RuntimeException(sprintf("Component %s was not rendered because there was no corrisponding picon:id in the markup.", $child->id));
                }
            }
        }
    }
    
    /**
     * @todo change this to a callback
     */
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
    
    /**
     * Renders this component
     */
    protected final function internalRenderComponent()
    {
        $markup = $this->getMarkup();
        
        if($markup==null)
        {
            throw new \MarkupNotFoundException(sprintf("Markup not found for component %s.", $this->id));
        }
        
        $this->onComponentTag($markup);
        $this->renderElementStart($markup);
        $this->onComponentTagBody($markup);
        $this->renderElementEnd($markup);
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
                $this->renderElement($element);
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
        $this->getMarkUpSource()->onComponentTag($this, $tag);
        if($this instanceof MarkupContainer && $this->hasChildren())
        {
            $tag->setTagType(new XmlTagType(XmlTagType::OPEN));
        }
        //@todo call onComponentTag for behaviours
    }
    
    /**
     * Render the body of the component
     * @param ComponentTag $tag 
     */
    protected function onComponentTagBody(ComponentTag $tag)
    {
        $this->getMarkUpSource()->onComponentTagBody($this, $tag);
    }
    
    /**
     * Checks that a component tag is a tag of the required name
     * @param ComponentTag $tag The tag to check
     * @param String $tagName The tag name that should match
     * @return Boolean whether or not the tag matches
     */
    protected function checkComponentTag(ComponentTag $tag, $tagName)
    {
        return $tag->getName()==$tagName;
    }
    
    /**
     * Checks that a component tag as an attribute and that the attribute has the required value
     * @param ComponentTag $tag The tag to check
     * @param String $attribute The attribute to find
     * @param String $value The value the attribute will have
     * @return Boolean True if the tag has the attribute with required value, false otherwise
     */
    protected function checkComponentTagAttribute(ComponentTag $tag, $attribute, $value)
    {
        $attributes = $tag->getAttributes();
        
        if(!array_key_exists($attribute, $attributes))
        {
            return false;
        }
        
        return $attributes[$attribute] == $value;
    }
    
    /**
     * Visit all the parent components of this components and execute
     * a callback on each
     * @param Identifier $identifier The identifier of the parent to look for
     * @param closure $callback The callback to run
     */
    public function visitParents(Identifier $identifier, $callback)
    {
        Args::callBackArgs($callback, 1);
        $this->internalVisitParents($identifier, $this->parent, $callback);
    }
    
    private function internalVisitParents(Identifier $identifier, $component, $callback)
    {
        Args::checkInstance($component, Component::getIdentifier());
        if($component!=null)
        {
            $response = new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL);
            if($component::getIdentifier()->of($identifier))
            {
                $response = $callback($component);
                Args::checkInstanceNotNull($response, VisitorResponse::getIdentifier());
            }
            if($response->equals(new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL)))
            {
                $this->internalVisitParents($identifier, $component->parent, $callback);
            }
        }
    }
    
    public function getPage()
    {
        $page = null;
        $callback = function($component) use (&$page)
        {
            $page = $component;
            return new VisitorResponse(VisitorResponse::STOP_TRAVERSAL);
        };
        $this->visitParents(WebPage::getIdentifier(), $callback);
        return $page;
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
    
    public static function getIdentifier()
    {
        return Identifier::forName(get_called_class());
    }
    
    /**
     * Gets whether or not this component is stateless
     * @return boolean
     */
    public function isStateless()
    {
        return true;
    }
    
    public function get($id)
    {
        if(empty($id))
        {
            return $this;
        }
       
        throw new \InvalidArgumentException("This component is not a container and does not have any children.");
    }
    
    /**
     * Generate a URL for a particular action:
     * 
     * WebPage Identifier - Generates a URL for the web page
     * WebPage Instance - Generate a URL for the page instance
     * 
     * @param mixed $for
     * @return type 
     */
    public function generateUrlFor($for)
    {
        if($for instanceof Listener)
        {
            return $this->urlForListener($for);
        }
        else if($for instanceof Identifier)
        {
            return $this->urlForPage($page);
        }
        else if($for instanceof WebPage)
        {
            return $this->urlForPageInstance($for);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf("generateUrlFor expected argment of type Identifier or Listener or WebPage, actual %s", get_class($for)));
        }
    }
    
    /**
     * @todo this should use a request target
     * @param Identifier $page
     * @return type 
     */
    public function urlForPage(Identifier $page)
    {
        if(is_subclass_of($page->getFullyQualifiedName(), WebPage::getIdentifier()->getFullyQualifiedName()))
        {
            return $this->getRequest()->getRootPath().$page->getFullyQualifiedName();
        }
        throw new \InvalidArgumentException(sprintf("Expected identifier of a web page, actual %s", $page->getFullyQualifiedName()));
    }
    
    
    public function urlForPageInstance(WebPage $pageInstance)
    {
        
    }
    
    /**
     * @todo this should use a request target
     * @param Identifier $page
     * @return type 
     */
    public function urlForListener(Listener $listener)
    {
        $target;
        $page = $this->getPage();
        if($page->isPageStateless())
        {
            $target = new PageRequestWithListenerTarget($page::getIdentifier(), $this->getComponentPath());
        }
        else
        {
            $target = new ListenerRequestTarget($this->getPage(), $this->getComponentPath());
        }
        return $this->getRequestCycle()->generateUrl($target);
    }
    
    public function getComponentPath()
    {
        $page = $this->getPage();
        if($page==null)
        {
            throw new \IllegalStateException(sprintf("Unable to generate a path for component %s as it has an incomplete hierarchy.", $this->id));
        }
        
        $path = $this->getId();
        
        $callback = function($component) use (&$path)
        {
            if(!($component instanceof WebPage))
            {
                $path = $component->getId().Component::PATH_SEPERATOR.$path;
                return new VisitorResponse(VisitorResponse::CONTINUE_TRAVERSAL);
            }
            return new VisitorResponse(VisitorResponse::STOP_TRAVERSAL);
        };
        $this->visitParents(Component::getIdentifier(), $callback);
        return str_replace(self::PATH_SEPERATOR.self::PATH_SEPERATOR, '', $path.self::PATH_SEPERATOR);
    }
    
    protected function newMarkupSource()
    {
        return new DefaultMarkupSource();
    }
    
    /**
     * Set the current page
     * @param mixed $page An instance of web page or an Identifier for a web page
     * @todo add page params
     * @todo add support for statefull pages
     * @todo some requests should redirect not rerender to keep urls looking nice
     */
    public function setPage($page)
    {
        if($page instanceof Identifier)
        {
            if($page->of(WebPage::getIdentifier()))
            {
                $target = new PageRequestTarget($page);
                $this->getRequestCycle()->addTarget($target);
            }
            else
            {
                throw new \InvalidArgumentException("Expected identifier to be for a web page");
            }
        }
        else if($page instanceof WebPage)
        {
            throw new \NotImplementedException();
        }
        else
        {
            throw new \InvalidArgumentException("setPage expects an identifier for a web page or an instance of a web page");
        }
    }
    
    /**
     * @todo add support for model inheritence (compound models)
     * @return Model The model for this component
     */
    public function getModel()
    {
        return $this->model;
    }
    
    protected function getMarkUpSource()
    {
        if($this->markupSource==null)
        {
            $this->markupSource = $this->newMarkupSource();
        }
        return $this->markupSource;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    public function getModelObject()
    {
        if($this->model!=null)
        {
            return $this->model->getModelObject();
        }
        return null;
    }
    
    public function __sleep()
    {
        $props = $this->getProperties();
        $key = array_search('parent', $props);
        if($key!=false)
        {
            unset($props[$key]);
        }
        
        return $props;
    }
}

?>
