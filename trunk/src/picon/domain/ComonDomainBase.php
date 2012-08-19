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
 * Super class for any and all domains
 *
 * @author Martin Cassidy
 * @package domain
 */
class ComonDomainBase
{
	/**
	 * Get the requested value
	 * @param String $name The name of the property to get
	 * @return Object The value of the property
	 */
	public function __get($name)
	{
		$obj = new \ReflectionClass($this);
		$property = $this->resolveProperty($obj, $name);
		if($property==null)
		{
			throw new \InvalidArgumentException ("Unknown property ".$name);
		}
		if(!$property->isPublic())
		{
			$property->setAccessible(true);
		}
		return $property->getValue($this);
	}

	/**
	 * Sets the value of a property
	 * @param String $name The name of the property to set
	 * @param Object $value The new value
	 */
	public function __set($name, $value)
	{
		$obj = new \ReflectionClass($this);
		$property = $this->resolveProperty($obj, $name);
		if($property==null)
		{
			throw new \InvalidArgumentException ("Unknown property ".$name);
		}
		if(!$property->isPublic())
		{
			$property->setAccessible(true);
		}
		$property->setValue($this, $value);
	}

	/**
	 * A generic to string
	 * TODO Add support for arrays
	 * @return string A string representation of the object
	 */
	public function __toString()
	{
		$reflection = new \ReflectionClass($this);
		$properties   = $reflection->getProperties();
		$out = "";
		foreach ($properties as $property)
		{
			if(!$property->isPublic())
			{
				$property->setAccessible(true);
			}
			$out .= $property->getName().": ".$property->getValue($this). "\n";
		}
		return $out;

	}

	private function resolveProperty(\ReflectionClass $obj, $name)
	{
		if($obj->hasProperty($name))
		{
			return $obj->getProperty($name);
		}
		else if($obj->getParentClass()!=null)
		{
			return $this->resolveProperty($obj->getParentClass(), $name);
		}
		return null;
	}
}

?>
