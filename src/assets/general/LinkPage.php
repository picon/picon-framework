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



/**
 * Description of LinkPage
 * 
 * @author Martin Cassidy
 */
class LinkPage extends AbstractPage
{
    private $text = 'Default text';
    
    public function __construct()
    {
        parent::__construct();
        $this->add(new picon\Label('text', new picon\PropertyModel($this, 'text')));
        
        $self = $this;
        $this->add(new \picon\Link('alterLink', function() use ($self)
        {
            $self->text = 'Update in callback text';
        }));
        
        $this->add(new \picon\Link('pageLink', function() use ($self)
        {
            $self->setPage(HomePage::getIdentifier());
        }));
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/general/LinkPage.php', 'assets/general/LinkPage.html');
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
