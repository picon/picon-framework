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
use picon\Link;
use picon\Identifier;

/**
 * Page shown when a request is made for a resource that should be in the session
 * but not found i.e. session must have expired
 *
 * @author Martin Cassidy
 * @package web/pages
 */
class SessionExpiredPage extends WebPage
{
	public function __construct()
	{
		parent::__construct();
		$me = $this;
		$this->add(new Link('home', function() use($me)
		{
			$this->setPage(Identifier::forName($me->getApplication()->getHomePage()));
		}));
	}
}

?>
