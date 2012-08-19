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
 * TODO finish adding state flags so that checks can be run to ensure overriden methods are calling
 * the parent implementation
 */
abstract class Component implements InjectOnWakeup, Identifiable, Detachable
{
	const TYPE_STRING = 'string';
	const TYPE_FLOAT = 'float';
	const TYPE_BOOL = 'boolean';
	const TYPE_DOUBLE = 'double';
	const TYPE_INT = 'integer';
	const TYPE_ARRAY = 'array';


	const VISITOR_CONTINUE_TRAVERSAL = 1;
	const VISITOR_STOP_TRAVERSAL = 2;
	const VISITOR_CONTINUE_TRAVERSAL_NO_DEEPER = 3;

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

	/**
	 * @var boolean true if this component has been rendered
	 */
	private $initialized = false;

	/**
	 * @var boolean true if the parent::onInitialize was called by all overriding
	 * implementations
	 */
	private $flagInitializeParentCall = false;


	private $beforePageRendered = false;

	private $model;

	/**
	 * @var boolean whether only the body of this component is to be rendered
	 */
	private $renderBodyOnly;

	/**
	 * @var boolean true if this component is in the hierarchy
	 */
	protected $added = false;

	private $markupSource = null;

	private $markupId;

	private $outputMarkupId = false;

	private $visible = true;

	private static $nextId = 0;

	/**
	 * @var boolean whether the component was added automatically
	 */
	private $auto = false;

	private $beforePageRenderCallback;
	private $afterPageRenderCallback;
	private $beforeComponentRenderCallback;
	private $afterComponentRenderCallback;
	private $onComponentTagCallback;
	private $onComponentTagBodyCallback;
	private $renderHeadCallback;

	const PATH_SEPERATOR = ':';

	/**
	 * Create a new component. Any overrides of the constructor must call the super.
	 * @param string $id the ID of this component
	 */
	public function __construct($id, Model $model = null)
	{
		Args::isString($id, 'id');
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

		foreach($this->behaviours as $behaviour)
		{
			$behaviour->bind($this);
		}

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
			throw new \InvalidArgumentException(sprintf("Argument %s was not a valid type for this method", gettype($object)));
		}
	}

	protected final function addBehaviour(Behaviour &$behaviour)
	{
		$this->behaviours['behaviour_'.$this->getNextComponentId()] = $behaviour;
		if($this->isInitialized())
		{
			$behaviour->bind($this);
		}
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
					$this->markup = $this->loadAssociatedMarkup();
					return $this->markup;
				}
				else
				{
					throw new \MarkupNotFoundException(sprintf("Component %s has no associated markup and no parent to get markup from", $this->id));
				}

			}
			else
			{
				$this->markup = $this->parent->getMarkupForChild($this);
				return $this->markup;
			}
		}
	}

	/**
	 * Called just before the page is rendered for all of its components
	 */
	public function beforePageRender()
	{
		if($this->beforePageRenderCallback!=null)
		{
			$callable = $this->beforePageRenderCallback;
			$callable($this);
		}
		$this->beforePageRendered = true;
	}

	/**
	 * Called just after a page is rendered
	 */
	public function afterPageRender()
	{
		if($this->afterPageRenderCallback!=null)
		{
			$callable = $this->afterPageRenderCallback;
			$callable($this);
		}
	}

	/**
	 * Called just before a component is rendered
	 */
	public function beforeComponentRender()
	{
		PiconApplication::get()->getComponentBeforeRenderListener()->onBeforeRender($this);
		$this->notifyBehavioursBeforeRender();
		if($this->beforeComponentRenderCallback!=null)
		{
			$callable = $this->beforeComponentRenderCallback;
			$callable($this);
		}
	}

	public function isInitialized()
	{
		return $this->initialized;
	}

	public function isBeforePageRender()
	{
		return $this->beforePageRendered;
	}

	/**
	 * Called just after the component is rendered
	 */
	public function afterComponentRender()
	{
		if($this->afterComponentRenderCallback!=null)
		{
			$callable = $this->afterComponentRender;
			$callable($this);
		}
		$this->rendered = true;
		PiconApplication::get()->getComponentAfterRenderListenersr()->onAfterRender($this);
		$this->notifyBehavioursAfterRender();
		 
		if($this instanceof MarkupContainer && $this->visible)
		{
			foreach($this->getChildren() as $child)
			{
				/**
				 * If the child component has not been rendered it must
				 * not have been present in the markup
				 * TODO move this into another non component parent calling method
				 * so that all children may have internalAfterRender
				 */
				if(!$child->rendered)
				{
					throw new \RuntimeException(sprintf("Component %s was not rendered because there was no corrisponding picon:id in the markup.", $child->id));
				}
			}
		}
	}

	public function render()
	{
		$exception = null;

		try
		{
			if($this->getParent()==null)
			{
				$this->beforePageRender();
			}
			$this->beforeComponentRender();
			$this->internalRender();
		}
		catch(Exception $ex)
		{
			$exception = $ex;
		}
		try
		{
			$this->afterComponentRender();
			if($this->getParent()==null)
			{
				$this->afterPageRender();
			}
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
		/* TODO this cloning is a quick fix, markup should be imutable until
		 * this point were a mutable version is created for use by the component
		* to render with for only this request
		*/
		$markup = clone $markup;

		//TODO add ajax placeholder to set display:none on invisible components
		if(!$this->visible)
		{
			return;
		}

		if($this->renderBodyOnly)
		{
			$this->onComponentTagBody($markup);
		}
		else
		{
			$this->onComponentTag($markup);
			$this->renderElementStart($markup);
			$this->onComponentTagBody($markup);
			$this->renderElementEnd($markup);
		}
	}

	/**
	 * Renders the start tag for the passed element
	 * @param MarkupElement The markup element to be rendered
	 */
	private final function renderElementStart(MarkupElement $element)
	{
		$this->getResponse()->write('<'.$element->getName());
		$this->renderAttributes($element->getAttributes());
		if($element->isOpenClose())
		{
			$this->getResponse()->write(' /');
		}
		$this->getResponse()->write('>');
	}

	/**
	 * Renders the close tag (if needed) for the element
	 * @param MarkupElement The markup element to be rendered
	 */
	private final function renderElementEnd(MarkupElement $element)
	{
		if($element->isOpen())
		{
			$this->getResponse()->write('</'.$element->getName().'>');
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
			$this->getResponse()->write(' '.$name.'="'.$value.'"');
		}
	}

	public function renderElement(MarkupElement $element)
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

		foreach($markup as &$element)
		{
			if($element instanceof ComponentTag)
			{
				if($this instanceof MarkupContainer)
				{
					$child = $this->get($element->getComponentTagId());

					if($child==null)
					{
						$child = ComponentResolverHelper::resolve($this, $element);

						if($child!=null && $child->getParent()==null)
						{
							$child->setAuto();
							$this->addComponent($child);
						}
						if($child!=null)
						{
							$child->setMarkup($element);
						}
					}

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
			elseif($element instanceof TextElement)
			{
				$this->getResponse()->write($element->getContent());
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
		if($this->onComponentTagCallback!=null)
		{
			$callable = $this->onComponentTagCallback;
			$callable($this, $tag);
		}
		$this->getMarkUpSource()->onComponentTag($this, $tag);
		if($this instanceof MarkupContainer && $this->hasChildren())
		{
			$tag->setTagType(new XmlTagType(XmlTagType::OPEN));
		}

		if($this->outputMarkupId)
		{
			$tag->put('id', $this->getMarkupId());
		}
		$this->notifyBehavioursComponentTag($tag);
	}

	/**
	 * Generates and returns a markup id for this component
	 * @param type $generate
	 */
	public function getMarkupId()
	{
		if(!isset($this->markupId))
		{
			$this->markupId = $this->id.$this->getNextComponentId();
		}
		return $this->markupId;
	}

	/**
	 * Manually set the markup id. Note, using this makes it your
	 * responsability to ensure the id is unique
	 * @param string $id
	 */
	public function setMarkupId($id)
	{
		$this->markupId = $id;
	}

	/**
	 *
	 * @param boolean $output
	 */
	public function setOutputMarkupId($output)
	{
		Args::isBoolean($output, 'output');
		$this->outputMarkupId = $output;
	}

	/**
	 * Render the body of the component
	 * @param ComponentTag $tag
	 */
	protected function onComponentTagBody(ComponentTag $tag)
	{
		if($this->onComponentTagBodyCallback!=null)
		{
			$callable = $this->onComponentTagBodyCallback;
			$callable($this, $tag);
		}
		$this->getMarkUpSource()->onComponentTagBody($this, $tag);
	}

	/**
	 * Checks that a component tag is a tag of the required name
	 * Throws an IllegalStateException if it is not
	 * @param ComponentTag $tag The tag to check
	 * @param String $tagName The tag name that should match
	 */
	protected function checkComponentTag(ComponentTag $tag, $tagName)
	{
		if($tag->getName()!=$tagName)
		{
			throw new \IllegalStateException(sprintf("An %s component can only be added to the HTML element %s", get_called_class(), $tagName));
		}
	}

	/**
	 * Checks that a component tag as an attribute and that the attribute has the required value
	 * Throws an IllegalStateException if it is not
	 * @param ComponentTag $tag The tag to check
	 * @param String $attribute The attribute to find
	 * @param String $value The value the attribute will have
	 */
	protected function checkComponentTagAttribute(ComponentTag $tag, $attribute, $value)
	{
		$attributes = $tag->getAttributes();

		if(!array_key_exists($attribute, $attributes) || $attributes[$attribute] != $value)
		{
			throw new \IllegalStateException(sprintf("An %s component can only be added to a tag with a %s of %s", get_called_class(), $attribute, $value));
		}
	}

	/**
	 * Visit all the parent components of this components and execute
	 * a callback on each
	 * @param Identifier $identifier The identifier of the parent to look for
	 * @param closure $callback The callback to run
	 */
	public function visitParents(Identifier $identifier, $callback)
	{
		Args::callBackArgs($callback, 1, 'callback');
		$this->internalVisitParents($identifier, $this->parent, $callback);
	}

	private function internalVisitParents(Identifier $identifier, $component, $callback)
	{
		if($component!=null)
		{
			$response = self::VISITOR_CONTINUE_TRAVERSAL;
			if($component::getIdentifier()->of($identifier))
			{
				$response = $callback($component);
			}
			if($response==self::VISITOR_CONTINUE_TRAVERSAL)
			{
				$this->internalVisitParents($identifier, $component->parent, $callback);
			}
		}
	}

	public function getPage()
	{
		$current = $this;
		while($current!=null)
		{
			if($current instanceof WebPage)
			{
				return $current;
			}
			$current = $current->getParent();
		}
		return null;
	}

	protected function setParent($parent)
	{
		$this->parent = $parent;
	}

	public final function getApplication()
	{
		return $GLOBALS['application'];
	}

	public final function getRequestCycle()
	{
		return $GLOBALS['requestCycle'];
	}

	public final function getRequest()
	{
		return $this->getRequestCycle()->getRequest();
	}

	public final function getResponse()
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
		foreach($this->behaviours as $behaviour)
		{
			if(!$behaviour->isStateless())
			{
				return false;
			}
		}
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
	 * TODO this should use a request target
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
	 * TODO this should use a request target
	 * @param Identifier $page
	 * @return type
	 */
	public function urlForListener(Listener $listener)
	{
		$target;
		$page = $this->getPage();

		$behaviour = null;

		if($listener instanceof Behaviour)
		{
			$behaviour = $listener->getBehaviourId();
		}

		if($page->isPageStateless())
		{
			$target = new PageRequestWithListenerTarget($page::getIdentifier(), $this->getComponentPath(), $behaviour);
		}
		else
		{
			$target = new ListenerRequestTarget($this->getPage(), $this->getComponentPath(), $behaviour);
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
				return Component::VISITOR_CONTINUE_TRAVERSAL;
			}
			return Component::VISITOR_STOP_TRAVERSAL;
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
	 * TODO add page params
	 */
	public function setPage($page)
	{
		Args::notNull($page, 'page');
		if($page instanceof Identifier)
		{
			if($page->of(WebPage::getIdentifier()))
			{
				$target = new PageRequestTarget($page);
			}
			else
			{
				throw new \InvalidArgumentException("Expected identifier to be for a web page");
			}
		}
		else if($page instanceof WebPage)
		{
			PageMap::get()->addOrUpdate($page);
			$target = new PageInstanceRequestTarget($page);
		}
		else
		{
			throw new \InvalidArgumentException(sprintf("setPage expects an identifier for a web page or an instance of a web page and not a %s", get_class($page)));
		}

		if($this->getRequestCycle()->containsTarget(ListenerRequestTarget::getIdentifier()))
		{
			$url = $this->getRequestCycle()->generateUrl($target);
			$this->getRequestCycle()->addTarget(new RedirectRequestTarget($url));
		}
		else
		{
			$this->getRequestCycle()->addTarget($target);
		}
	}

	/**
	 * TODO add support for model inheritence (compound models)
	 * @return Model The model for this component
	 */
	public function getModel()
	{
		if($this->model==null)
		{
			$model = null;
			$current = $this->getParent();
			while($current!=null)
			{
				if($current->model!=null && $current->model instanceof ComponentInheritedModel)
				{
					$model = $current->model;
					break;
				}
				$current = $current->getParent();
			}

			if($model!=null)
			{
				$model = $model->onInherit($this);

				if($model!=null)
				{
					$this->model = $model;
					$this->model->bind($this);
				}
			}
		}
		return $this->model;
	}

	public function setMarkup(MarkupElement $markup)
	{
		$this->markup = $markup;
	}

	public function setModel(Model &$model)
	{
		$this->model = $model;
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

	public function setModelObject(&$object)
	{
		if($this->getModel()!=null)
		{
			$this->getModel()->setModelObject($object);
		}
	}

	public function getModelObject()
	{
		if($this->getModel()!=null)
		{
			return $this->getModel()->getModelObject();
		}
		return null;
	}

	private static function getNextComponentId()
	{
		self::$nextId++;
		return dechex(self::$nextId);
	}

	/**
	 * TODO should really create converters for primatives
	 * @return string a representation of the model object as a string
	 */
	public function getModelObjectAsString()
	{
		$object = $this->getModelObject();
		if(is_object($object))
		{
			$converter = $this->getApplication()->getConverter(get_class($object));

			if($converter==null)
			{
				throw new \RuntimeException(sprintf("Unable to find converter for type %s", get_class($object)));
			}
			$string = $converter->convertToString($object);

			if(!is_string($string))
			{
				throw new \RuntimeException("Convert did not correctly convert to string");
			}
			return $string;
		}
		else if(is_array($object))
		{
			throw new \RuntimeException("getModelObjectAsString() does not support array");
		}
		else if(is_bool($object))
		{
			return $object ? 'true':'false';
		}
		else
		{
			settype($object, 'string');
			return $object;
		}
	}

	public function fatel($message)
	{
		FeedbackModel::get()->addMessage(new FeedbackMessage(FeedbackMessage::FEEDBACK_MEESAGE_FATEL, $message, $this));
	}

	public function error($message)
	{
		FeedbackModel::get()->addMessage(new FeedbackMessage(FeedbackMessage::FEEDBACK_MEESAGE_ERROR, $message, $this));
	}

	public function warning($message)
	{
		FeedbackModel::get()->addMessage(new FeedbackMessage(FeedbackMessage::FEEDBACK_MEESAGE_WARNING, $message, $this));
	}

	public function info($message)
	{
		FeedbackModel::get()->addMessage(new FeedbackMessage(FeedbackMessage::FEEDBACK_MEESAGE_INFO, $message, $this));
	}

	public function success($message)
	{
		FeedbackModel::get()->addMessage(new FeedbackMessage(FeedbackMessage::FEEDBACK_MEESAGE_SUCCESS, $message, $this));
	}

	public function hasMessage($level = null)
	{
		return FeedbackModel::get()->hasMessages($this, $level);
	}

	public function hasErrorMessage()
	{
		return FeedbackModel::get()->hasMessages($this, FeedbackMessage::FEEDBACK_MEESAGE_ERROR);
	}

	private function notifyBehavioursBeforeRender()
	{
		foreach($this->behaviours as $behaviour)
		{
			$behaviour->beforeRender($this);
		}
	}

	private function notifyBehavioursAfterRender()
	{
		foreach($this->behaviours as $behaviour)
		{
			$behaviour->afterRender($this);
		}
	}

	private function notifyBehavioursComponentTag(ComponentTag $tag)
	{
		foreach($this->behaviours as $behaviour)
		{
			$behaviour->onComponentTag($this, $tag);
		}
	}

	/**
	 * Called by the header container when the HTML <head> is rendering
	 * @param HeaderContainer $container
	 * @param HeaderResponse $headerResponse
	 */
	public final function renderHeadContainer(HeaderContainer $container, HeaderResponse $headerResponse)
	{
		$this->getMarkUpSource()->renderHead($this, $container, $headerResponse);
		$this->renderHead($headerResponse);

		foreach($this->behaviours as $behaviour)
		{
			$behaviour->renderHead($this, $container, $headerResponse);
		}
	}

	/**
	 * Called for each component when the HTML <head> is rendering.
	 * @param HeaderResponse $headerResponse The response to write to
	 */
	public function renderHead(HeaderResponse $headerResponse)
	{
		if($this->renderHeadCallback!=null)
		{
			$callable = $this->renderHeadCallback;
			$callable($this, $headerResponse);
		}
	}

	/**
	 * Sets whether this component will render its open and close
	 * tags
	 * @param boolean $renderBodyOnly
	 */
	public function setRenderBodyOnly($renderBodyOnly)
	{
		Args::isBoolean($renderBodyOnly, 'renderBodyOnly');
		$this->renderBodyOnly = $renderBodyOnly;
	}

	public function setVisible($visible)
	{
		Args::isBoolean($visible, 'visible');
		$this->visible = $visible;
	}

	public function getBehaviours()
	{
		return $this->behaviours;
	}

	public function getBehaviourById($id)
	{
		if(array_key_exists($id, $this->behaviours))
		{
			return $this->behaviours[$id];
		}
		return null;
	}

	private function setAuto()
	{
		$this->auto = true;
	}


	public function isAuto()
	{
		return $this->auto;
	}

	public function detach()
	{

	}

	public function getLocalizer()
	{
		return Localizer::get($this);
	}

	public function getComponentKey($suffix)
	{
		return sprintf("%s.%s.%s", get_class($this->getPage()), $this->id, $suffix);
	}

	public function isRendered()
	{
		return $this->rendered;
	}

	public function setBeforePageRenderCallback($beforePageRenderCallback)
	{
		Args::callBackArgs($beforePageRenderCallback, 1, 'beforePageRenderCallback');
		$this->beforePageRenderCallback = $beforePageRenderCallback;
	}

	public function setBeforeComponentRenderCallback($beforeComponentRenderCallback)
	{
		Args::callBackArgs($beforeComponentRenderCallback, 1, 'beforeComponentRenderCallback');
		$this->beforeComponentRenderCallback = $beforeComponentRenderCallback;
	}

	public function setAfterPageRenderCallback($afterPageRenderCallback)
	{
		Args::callBackArgs($afterPageRenderCallback, 1, 'afterPageRenderCallback');
		$this->afterPageRenderCallback = $afterPageRenderCallback;
	}

	public function setAfterComponentRenderCallback($afterComponentRenderCallback)
	{
		Args::callBackArgs($afterComponentRenderCallback, 1, 'afterComponentRenderCallback');
		$this->afterComponentRenderCallback = $afterComponentRenderCallback;
	}

	public function setOnComponentTagCallback($onComponentTagCallback)
	{
		Args::callBackArgs($onComponentTagCallback, 2, 'onComponentTagCallback');
		$this->onComponentTagCallback = $onComponentTagCallback;
	}

	public function setOnComponentTagBodyCallback($onComponentTagBodyCallback)
	{
		Args::callBackArgs($onComponentTagBodyCallback, 2, 'onComponentTagBodyCallback');
		$this->onComponentTagBodyCallback = $onComponentTagBodyCallback;
	}

	public function setRenderHeadCallback($renderHeadCallback)
	{
		Args::callBackArgs($renderHeadCallback, 2, 'renderHeadCallback');
		$this->renderHeadCallback = $renderHeadCallback;
	}
}

?>
