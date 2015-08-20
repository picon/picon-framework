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

namespace picon\web;

/**
 * A link which, when clicked, will set the page of an associated paginatable
 * component to the specified value
 * 
 * @author Martin Cassidy
 * @package web/markup/html/paging
 */
class NavigationLink extends Link
{
    private $pageable;
    private $page;
    
    public function __construct($id, Pageable $pageable, $page)
    {
        parent::__construct($id, function(){});
        Args::isNumeric($page, 'page');
        $this->pageable = $pageable;
        $this->page = $page;
    }
    
    protected function onLinkClicked()
    {
        $this->pageable->setCurrentPage($this->page);
    }
}

?>
