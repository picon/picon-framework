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

use picon\AjaxLink;
use picon\MarkupContainer;
use picon\PropertyModel;
use picon\Label;
use picon\CallbackAjaxCallDecorator;

/**
 * Description of AjaxPage
 *
 * @author Martin Cassidy
 */
class AjaxPage extends AbstractPage
{
    private $value = 'starting value';
    
    public function __construct()
    {
        parent::__construct();
        
        $update = new Label('toUpdate', new PropertyModel($this, 'value'));
        $update->setOutputMarkupId(true);
        $this->add($update);
        $self = $this;
        
        $link = new AjaxLink('ajaxLink', function($target) use ($self, $update)
        {
            $self->value = 'ajax value';
            $target->add($update);
        });
        
        $this->add($link);
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}

?>
