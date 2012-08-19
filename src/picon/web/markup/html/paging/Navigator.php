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
 * A list of navigation links to allow the choice of all pages of a
 * paginatable component.
 *
 * TODO this will get longer and longer if there are too many pages, the
 * amount should be restricted and the page links shown varied based on current page
 * @author Martin Cassidy
 * @package web/markup/html/paging
 */
class Navigator extends Panel
{
	private $pageable;

	public function __construct($id, Pageable $pageable)
	{
		parent::__construct($id);
		$this->pageable = $pageable;

		$pageLinks = new RepeatingView('page');
		$this->add($pageLinks);

		for($i = 0; $i < $this->pageable->getPageCount(); $i++)
		{
			$linkBlock = new MarkupContainer($pageLinks->getNextChildId());
			$link = new NavigationLink('pageLink', $pageable, $i+1);
			$linkBlock->add($link);
			$link->add(new Label('pageNumber', new BasicModel($i+1)));
			$pageLinks->add($linkBlock);
		}
	}
}

?>
