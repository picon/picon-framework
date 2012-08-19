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
 * 
 * $HeadURL$
 * $Revision$
 * $Author$
 * $Date$
 * $Id$
 * 
 * */

/**
 * Description of TabPanelPage
 * 
 * @author Martin Cassidy
 */
class TabPanelPage extends AbstractPage
{
    public function __construct()
    {
        parent::__construct();
        $collection = new picon\TabCollection();
        
        $collection->addTab('One', function($id)
        {
            return new TabOnePanel($id);
        });
        $collection->addTab('Two', function($id)
        {
            return new TabTwoPanel($id);
        });
        
        $this->add(new picon\TabPanel('tabs', $collection));
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/general/TabPanelPage.php', 'assets/general/TabPanelPage.html', 'assets/general/tabs/TabOnePanel.php', 'assets/general/tabs/TabOnePanel.html', 'assets/general/tabs/TabTwoPanel.php', 'assets/general/tabs/TabTwoPanel.html');
    }
}

?>
