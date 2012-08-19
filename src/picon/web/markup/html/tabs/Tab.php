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
 * A tab domain for use with Tab Panel
 *
 * @see TabPanel
 * @see TabCollection
 * @author Martin Cassidy
 * @package web/markup/html/tabs
 */
class Tab extends ComonDomainBase
{
	private $name;
	private $newMethod;

	/**
	 *
	 * @param string $name
	 * @param closure $newMethod
	 */
	public function __construct($name, $newMethod)
	{
		Args::isString($name, 'name');
		Args::callBackArgs($newMethod, 1, 'newMethod');
		$this->name = $name;
		$this->newMethod = new SerializableClosure($newMethod);
	}

	public function newTab($id)
	{
		$method = $this->newMethod->getReflection();
		return $method->invokeArgs(array($id));
	}
}

?>
