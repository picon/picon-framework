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

/**
 * Abstract behaviour provideds default implementation for each behaviour method
 * whilst also providing the functionality needed to obtain information about a behaviour
 * 
 * @author Martin Cassidy
 * @package web/behaviour
 */
abstract class AbstractBehaviour implements Behaviour, Identifiable
{
    private $component;
    
    public function bind(Component &$component)
    {
        $this->component = $component;
    }
    
    public function afterRender(Component &$component)
    {
        
    }
    
    public function beforeRender(Component &$component)
    {
        
    }
    
    public function onComponentTag(Component &$component, ComponentTag &$tag)
    {
        
    }
    
    public function renderHead(Component &$component, HeaderContainer $headerContainer, HeaderResponse $headerResponse)
    {
        
    }
    
    public static function getIdentifier()
    {
        return Identifier::forName(get_called_class());
    }
    
    public function isStateless()
    {
        return true;
    }
    
    /**
     * Gets the id of the behaviour within its component
     */
    public function getBehaviourId()
    {
        foreach($this->component->getBehaviours() as $index => $behaviour)
        {
            if($behaviour==$this)
            {
                return $index;
            }
        }
        throw new \IllegalStateException('This behaviour was not found in the component it was bound to.');
    }
    
    public function getComponent()
    {
        return $this->component;
    }
}

?>
