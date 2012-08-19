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

use picon\WebPage;
use picon\HeaderResponse;
use picon\Link;

/**
 * Description of AbstractPage
 * 
 * @author Martin Cassidy
 */
abstract class AbstractPage extends WebPage
{
    public function __construct()
    {
        parent::__construct();
        $self = $this;
        $this->add(new Link('home', function() use ($self)
        {
            $self->setPage(HomePage::getIdentifier());
        }));
        
        $files = array('index.php', 'assets/AbstractPage.php', 'assets/AbstractPage.html', 'assets/SamplePageClassAuthorisationStrategy.php');
        $files = array_merge($files, $this->getInvolvedFiles());
        
        $sourceLink = new Link('source', function() use ($self, $files)
        {
            $self->setPage(new SourcePage($files));
        });
        $sourceLink->setPopupSettings(new \picon\PopupSettings('Source Code', '900px', '600px'));
        $this->add($sourceLink);
    }
    
    
    public abstract function getInvolvedFiles();
    
    public function renderHead(HeaderResponse $headerResponse)
    {
        $headerResponse->renderCSS('css/main.css');
    }
}

?>
