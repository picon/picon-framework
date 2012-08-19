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
 * A panel topped by a list of links. The panel changes when each link is clicked
 * TODO finish off the class attributes for this
 * @author Martin Cassidy
 * @package web/markup/html/tabs
 */
class TabPanel extends Panel
{
    private $collection;
    private $selctedTab = 0;
    const PANEL_ID = 'panel';
    
    public function __construct($id, TabCollection $collection)
    {
        parent::__construct($id);
        $this->collection = $collection;   
        $this->setup();
    }
    
    protected function getCollection()
    {
        return $this->collection;
    }
    
    protected function setup()
    {
        $this->setSelectedTab(0);
    }
    
    protected function onInitialize()
    {
        parent::onInitialize();
        $me = $this;
        //TODO add the type hint back into the closure when the serializer can handle them
        $this->add(new ListView("tab", function($item) use ($me)
        {
            $tab = $item->getModelObject();
            $link = $me->newLink('link', $item->getIndex());
            if($me->getSelectedTab()==$item->getIndex())
            {
                $item->add(new \picon\AttributeAppender('class', new \picon\BasicModel('selected'), ' '));
            }
            
            $item->add($link);
            $link->add(new \picon\Label('name', new \picon\BasicModel($tab->name)));
        }, new ArrayModel($this->collection->tabs)));
    }
    
    public function newLink($id, $index)
    {
        $me = $this;
        return new \picon\Link($id, function() use ($me, $item, $index)
        {
            $me->setSelectedTab($index);
        });
    }
    
    private function getPanelForSelected()
    {
        $tabs = $this->collection->tabs;
        if(count($tabs)<1)
        {
            return new EmptyPanel(self::PANEL_ID);
        }
        else
        {
            $tab = $tabs[$this->selctedTab];
            return $tab->newTab(self::PANEL_ID);
        }
    }
    
    public function setSelectedTab($tabIndex)
    {
        $this->selctedTab = $tabIndex;
        $this->addOrReplace($this->getPanelForSelected()); 
    }
    
    public function getSelectedTab()
    {
        return $this->selctedTab;
    }
}

?>
