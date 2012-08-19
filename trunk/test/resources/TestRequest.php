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

use picon\Request;

/**
 * Description of TestRequest
 *
 * @author Martin Cassidy
 */
class TestRequest implements Request
{
	private $get;
	private $post;
	private $ajax;
	private $resource;
	private $path;
	const ROOT_PATH = "/picon";

	public function __construct($path, $get = null, $post = null, $ajax = false, $resource = false)
	{
		$this->path = $path;
		$this->get = $get;
		$this->post = $post;
		$this->ajax = $ajax;
		$this->resource = $resource;
	}

	public function getParameter($name)
	{
		if($this->get==false)
		{
			return null;
		}
		if(array_key_exists($name, $this->get))
		{
			return $this->get[$name];
		}
		return null;
	}

	public function getPostedParameter($name)
	{
		if($this->post==false)
		{
			return null;
		}
		if(array_key_exists($name, $this->post))
		{
			return $this->post[$name];
		}
		return null;
	}

	public function getQueryString()
	{
		//Test requests don't have query strings
		return null;
	}

	public function isAjax()
	{
		return $this->ajax;
	}

	public function isGet()
	{
		return $this->get!=null;
	}

	public function isPost()
	{
		return $this->post!=null;
	}

	public function isResourceRequest()
	{
		return $this->resource;
	}

	public function getRootPath()
	{
		return self::ROOT_PATH;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getParameters()
	{
		return $this->get;
	}

	public function getPostParameters()
	{
		return $this->post;
	}
}

?>
