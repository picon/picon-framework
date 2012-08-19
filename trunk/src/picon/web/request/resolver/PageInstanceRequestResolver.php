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
 * Resolver for a page instance that has already been created
 *
 * @author Martin Cassidy
 * @package web/request/resolver
 */
class PageInstanceRequestResolver implements RequestResolver
{
	public function matches(Request $request)
	{
		if($request->getParameter('picon-resource')==null && $request->getParameter('listener')==null && $request->getParameter('pageid')!=null)
		{
			return true;
		}
		return false;
	}

	public function resolve(Request $request)
	{
		$page = PageMap::get()->getPageById($request->getParameter('pageid'));
		return new PageInstanceRequestTarget($page);
	}

	public function generateUrl(RequestTarget $target)
	{
		if($target instanceof PageInstanceRequestTarget)
		{
			$page = $target->getPage();
			PageMap::get()->addOrUpdate($page);
			return '?pageid='.$page->getId();
		}
		else
		{
			throw new \InvalidArgumentException('Expecting PageInstanceRequestTarget');
		}
	}

	public function handles(RequestTarget $target)
	{
		return $target instanceof PageInstanceRequestTarget;
	}
}

?>
