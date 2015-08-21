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

use picon\web\HeaderResponse;
use picon\web\WebPage;
use picon\web\ListItem;
use picon\web\ListView;
use picon\web\ArrayModel;
use picon\web\AjaxRequestTarget;
use picon\web\MarkupContainer;
use picon\web\Label;

/**
 * Description of SourcePage
 * 
 * @author Martin Cassidy
 */
class SourcePage extends WebPage
{
    private $panel;
    public function __construct($files)
    {
        parent::__construct();
        $self = $this;
        $this->add(new ListView('files', function(ListItem $item) use ($self)
        {
            $link = new AjaxLink('link', function(AjaxRequestTarget $target) use ($item, $self)
            {
                $target->add($self->getPanel());
                $newPanel = new CodeOutputPanel('code', $item->getModelObject());
                $newPanel->setOutputMarkupId(true);
                $self->getPanel()->addOrReplace($newPanel);
            });
            $item->add($link);
            $link->add(new Label('fileName', $item->getModel()));
        }, new ArrayModel($files)));
        
        $this->panel = new MarkupContainer('wrapper');
        $this->add($this->panel);
        $this->panel->setOutputMarkupId(true);
        $this->panel->add(new CodeOutputPanel('code', $files[0]));
    }
    
    public function getPanel()
    {
        return $this->panel;
    }
    
    public function renderHead(HeaderResponse $headerResponse)
    {
        parent::renderHead($headerResponse);
        $headerResponse->renderCSSFile('css/source.css');
    }
}

?>
