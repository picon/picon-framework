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

use picon\WebPage;

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
        $this->add(new picon\ListView('files', function(\picon\ListItem $item) use ($self)
        {
            $link = new \picon\AjaxLink('link', function(picon\AjaxRequestTarget $target) use ($item, $self)
            {
                $target->add($self->getPanel());
                $newPanel = new CodeOutputPanel('code', $item->getModelObject());
                $newPanel->setOutputMarkupId(true);
                $self->getPanel()->addOrReplace($newPanel);
            });
            $item->add($link);
            $link->add(new picon\Label('fileName', $item->getModel()));
        }, new picon\ArrayModel($files)));
        
        $this->panel = new picon\MarkupContainer('wrapper');
        $this->add($this->panel);
        $this->panel->setOutputMarkupId(true);
        $this->panel->add(new CodeOutputPanel('code', $files[0]));
    }
    
    public function getPanel()
    {
        return $this->panel;
    }
    
    public function renderHead(picon\HeaderResponse $headerResponse)
    {
        parent::renderHead($headerResponse);
        $headerResponse->renderCSSFile('css/source.css');
    }
}

?>
