<?php

/**
 * Podium CMS
 * http://code.google.com/p/podium/
 *
 * Copyright (C) 2011-2012 Martin Cassidy <martin.cassidy@webquub.com>

 * Podium CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Podium CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Podium CMS.  If not, see <http://www.gnu.org/licenses/>.
 * */

namespace picon\web\ajax\markup\html;

use picon\web\MarkupContainer;
use picon\web\EmptyPanel;
use picon\web\Panel;
use picon\web\DialogBehavior;
use picon\web\ComponentTag;
use picon\web\AjaxRequestTarget;
use picon\Args;

/**
 * Panel which implements the jQuery UI dialog
 * 
 * @todo problems arise when adding the entire panel to the ajax request target
 * @author Martin Cassidy
 */
class ModalWindow extends Panel
{
    private $dialog;
    private $style = 'dispaly:none;';
    private $wrapper;
    private $panel;
    
    public function __construct($id)
    {
        parent::__construct($id);
        $this->wrapper = new MarkupContainer('wrapper');
        $this->wrapper->setOutputMarkupId(true);
        $this->add($this->wrapper);
        $this->dialog = new DialogBehavior();
        $this->dialog->setModal(true);
        $this->dialog->setAutoOpen(false);
        $this->setOutputMarkupId(true);
        $this->setContent(new EmptyPanel($this->getContentId()));
        $this->add($this->dialog);
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        $tag->setName('div');
        parent::onComponentTag($tag);
    }
    
    public function getContentId()
    {
        return 'content-panel';
    }
    
    public function setContent(Panel $panel)
    {
        $this->wrapper->addOrReplace($panel);
    }
    
    public function show(AjaxRequestTarget $target)
    {
        $target->add($this->wrapper);
        $target->executeScript(sprintf("$('#%s').dialog('open');", $this->getMarkupId()));
    }
    
    public function hide(AjaxRequestTarget $target)
    {
        $target->executeScript(sprintf("$('#%s').dialog('close');", $this->getMarkupId()));
    }
    
    public function setHeight($height)
    {
        Args::isNumeric($height, 'height');
        $this->dialog->setHeight($height);
    }
    
    public function setWidth($width)
    {
        Args::isNumeric($width, 'width');
        $this->dialog->setWidth($width);
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
