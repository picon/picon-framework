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

namespace picon\web\markup\html\form;
use picon\core\Types;
use picon\web\domain\ComponentTag;
use picon\web\model\Model;

/**
 * A text field component
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class TextField extends AbstractTextComponent
{
    private $type;
    public function __construct($id, Model $model = null, $type = null)
    {
        parent::__construct($id, $model);
        if($type==null)
        {
            $type = Types::TYPE_STRING;;
        }
        $this->type = $type;
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        $this->checkComponentTag($tag, 'input');
        $this->checkComponentTagAttribute($tag, 'type', $this->getTypeAttribute());
        parent::onComponentTag($tag);
        $tag->put('value', $this->getValue());
    }
    
    /**
     * Get the data type for this text component
     */
    protected function getType()
    {
        return $this->type;
    }
    
    protected function getTypeAttribute()
    {
        return 'text';
    }
}

?>
