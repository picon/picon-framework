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
 * Super class for ajax behavours
 * Adds in the ajax resources that are needed and provideds methods for generating
 * callback urls
 *
 * @author Martin Cassidy
 * @package web/ajax
 */
abstract class AbstractAjaxBehaviour extends AbstractBehaviour implements BehaviourListener
{
    private $callDecorator;
    
    public function __construct()
    {
        PiconApplication::get()->addComponentRenderHeadListener(new JQueryRenderHeadListener());
    }
    
    public function bind(Component &$component)
    {
        parent::bind($component);
        $component->setOutputMarkupId(true);
    }
    
    public function renderHead(Component &$component, HeaderContainer $headerContainer, HeaderResponse $headerResponse)
    {
        parent::renderHead($component, $headerContainer, $headerResponse);
        $headerResponse->renderJavaScriptResourceReference(new ResourceReference('ajax.js', self::getIdentifier()));
    }
    
    public function isStateless()
    {
        return false;
    }
    
    protected function generateCallbackScript()
    {
        $decorator = $this->getAjaxCallDecorator();
        
        $callScript = $this->callScript();
        $successScript = $this->successScript();
        $failScript = $this->failScript();
        
        if($decorator!=null && $decorator instanceof AjaxCallDecorator)
        {
            $callScript = $decorator->decorateScript($callScript);
            $successScript = $decorator->decorateSuccessScript($successScript);
            $failScript = $decorator->decorateFailScript($failScript);
        }
        
        $successScript = sprintf('function() {%s}', $successScript);
        $failScript = sprintf('function() {%s}', $failScript);
        
        $url = $this->getComponent()->urlForListener($this).'&ajax=ajax';
        $script = $this->generateCallScript($url);
        $script = sprintf('%s'.$script.', %s, %s);', $callScript, $successScript, $failScript);
        
        return $script;
    }
    
    /**
     * Should return the partial ajax script, that is the js function to call
     * and the paramaters pass it up the call decoratos, should not include a trailing
     * ) or ,
     */
    protected function generateCallScript($url)
    {
        return sprintf('piconAjaxGet(\'%s\'', $url);
    }
    
    public function onEvent()
    {
        $target = new AjaxRequestTarget();
        $this->getComponent()->getRequestCycle()->addTarget($target);
        $this->onEventCallback($target);
    }
    
    //@todo rename this method when listeners can support their own method names and are not fixed
    //to onEvent()
    public abstract function onEventCallback(AjaxRequestTarget $target);
    
    protected function getAjaxCallDecorator()
    {
        return $this->callDecorator;
    }
    
    protected function successScript()
    {
        return "";
    }
    
    protected function failScript()
    {
        return "";
    }
    
    protected function callScript()
    {
        return "";
    }
    
    public function setAjaxCallDecorator(AjaxCallDecorator &$decorator)
    {
        $this->callDecorator = $decorator;
    }
}

?>
