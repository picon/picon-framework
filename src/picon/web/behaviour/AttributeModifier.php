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

namespace picon\web\behaviour;

use picon\web\AbstractBehaviour;
use picon\web\Component;
use picon\web\domain\ComponentTag;
use picon\web\model\Model;

/**
 * Description of AttributeModifier
 * 
 * @author Martin Cassidy
 */
class AttributeModifier extends AbstractBehaviour
{
    private $attributeName;
    private $value;
    
    public function __construct($attributeName, Model $value)
    {
        $this->attributeName = $attributeName;
        $this->value = $value;
    }
    
    public function onComponentTag(Component &$component, ComponentTag &$tag)
    {
        parent::onComponentTag($component, $tag);
        $attrs = $tag->getAttributes();
        $current = '';
        if(array_key_exists($this->attributeName, $attrs))
        {
            $current = $attrs[$this->attributeName];
        }
        $tag->put($this->attributeName, $this->newValue($current));
    }
    
    protected function getValue()
    {
        return $this->value;
    }
    
    protected function newValue($current)
    {
        return $this->getValue()->getModelObject();
    }
}

?>
