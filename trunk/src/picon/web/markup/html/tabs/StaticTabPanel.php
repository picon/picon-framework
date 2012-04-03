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
 * A tab panel whos links do not change page but simple swap 
 * the visibility of an inner panel.
 * 
 * @author Martin Cassidy
 */
class StaticTabPanel extends TabPanel
{
    private $indexes = array();
    
    /**
     * @todo get rid of this when listener is finished
     */
    public function __wakeup()
    {
        parent::__wakeup();
        PiconApplication::get()->addComponentRenderHeadListener(new JQueryRenderHeadListener());
    }
    
    protected function setup()
    {
        PiconApplication::get()->addComponentRenderHeadListener(new JQueryRenderHeadListener());
        $view = new RepeatingView(TabPanel::PANEL_ID);
        $this->add($view);
        foreach($this->getCollection()->tabs as $index => $tabItem)
        {
            $panel = $tabItem->newTab($view->getNextChildId());
            $panel->setOutputMarkupId(true);
            $this->indexes[$index] = $panel->getMarkupId();
            $panel->add(new AttributeModifier('style', new BasicModel('display:none;')));
            $view->add($panel);
        }
    }
    
    public function newLink($id, $index)
    {
        $switchLink = new MarkupContainer($id);
        $switchLink->add(new AttributeModifier('href', new BasicModel(sprintf('javascript:;', $this->indexes[$index]))));
        $switchLink->add(new AttributeModifier('onClick', new BasicModel(sprintf('$(\'#\'+currentTab).hide(); $(\'#%s\').show();currentTab = \'%s\'; $(\'li.selected\', $(this).parents(\'ul\').first()).removeClass(\'selected\'); $(this).parents(\'li\').first().addClass(\'selected\');', $this->indexes[$index], $this->indexes[$index]))));
        return $switchLink;
    }
    
    public function renderHead(HeaderResponse $headerResponse)
    {
        parent::renderHead($headerResponse);
        $headerResponse->renderScript(sprintf('var currentTab; $(document).ready(function(){$(\'#%s\').show(); currentTab = \'%s\';});', $this->indexes[0], $this->indexes[0]));
    }
}

?>
