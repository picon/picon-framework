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
 * Description of DefaultJQueryUIBehaviour
 *
 * @author Martin Cassidy
 */
class DefaultJQueryUIBehaviour extends AbstractJQueryUIBehaviour
{
    private $function;
    private $options;
    
    public function __construct($function, $options = null)
    {
        parent::__construct();
        $this->function = $function;
        
        if($options!=null)
        {
            if($options instanceof Options)
            {
                $this->options = $options;
            }
            else
            {
                throw new \InvalidArgumentException('DefaultJQueryUIBehaviour::__construct expected argument 2 to be an an Options object');
            }
        }
        else
        {
            $this->options = new Options();
        }
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }
    
    public function bind(Component &$component)
    {
        parent::bind($component);
        $component->setOutputMarkupId(true);
    }
    
    public function renderHead(Component &$component, HeaderContainer $headerContainer, HeaderResponse $headerResponse)
    {
        parent::renderHead($component, $headerContainer, $headerResponse);
        $headerResponse->renderScript(sprintf("\$(document).ready(function(){\$('#%s').%s(%s);});", $this->getComponent()->getMarkupId(), $this->function, $this->getOptions()->render($this)));
    }
    
    public function onEvent()
    {
        $property = RequestCycle::get()->getRequest()->getParameter('property');
        
        if($property!=null)
        {
            $callbackOption = $this->getOptions()->getOption($property);
            
            if($callbackOption!=null && $callbackOption instanceof CallbackOption)
            {
                $target = new AjaxRequestTarget();
                $this->getComponent()->getRequestCycle()->addTarget($target);
                $callbackOption->call($target);
            }
        }
    }
}

?>
