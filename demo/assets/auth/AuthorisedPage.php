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

use picon\web\Link;

/**
 * Description of AuthorisedPage
 * 
 * @author Martin Cassidy
 */
class AuthorisedPage extends AbstractAuthorisedPage
{
    public function __construct()
    {
        parent::__construct();
        $self = $this;
        $this->add(new Link('link', function() use ($self)
        {
            $_SESSION['auth'] = false;
            $self->setPage(HomePage::getIdentifier());
        }));
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/auth/AuthorisedPage.php', 'assets/auth/AuthorisedPage.html');
    }
}

?>
