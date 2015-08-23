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

use picon\core\domain\Identifier;

/**
 * Can be thrown at any time during the life cycle of a component or a request
 * target response. It will cause the request target to truncate the request
 * target stack and create a new single PageRequestTarget for a page of
 * the given identifier
 * 
 * @author Martin Cassidy
 * @package web/exceptions
 */
class RestartRequestOnPageException extends \RuntimeException
{
    private $page;
    public function __construct(Identifier $page)
    {
        if(!$page->of(WebPage::getIdentifier()))
        {
            throw new \InvalidArgumentException(sprintf('RestartRequestOnPageException expects a web page identifier, %s given.', $page->getFullyQualifiedName()));
        }
        $this->page = $page;
        parent::__construct('RestartRequestOnPageException');
    }
    
    public function getPageIdentifier()
    {
        return $this->page;
    }
}

?>
