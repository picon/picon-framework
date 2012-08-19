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
 * Represents a collection of listeners that are treated as one listener
 *
 * @author Martin Cassidy Cassidy
 * @package listeners
 */
abstract class ListenerCollection
{
	private $listeners = array();

	public function add($listener)
	{
		if(!$this->validateListener($listener))
		{
			throw new \InvalidArgumentException(sprintf("Listener of type %s was nat valid for the listener collection", get_class($listener)));
		}
		array_push($this->listeners, $listener);
	}

	public function remove($listener)
	{
		$index = null;
		array_walk($this->listeners, function($value, $key) use($listener, &$index)
		{
			if($value==$listener)
			{
				$index = $key;
			}
		});
		if($index!=null)
		{
			unset($this->listeners[$index]);
		}
	}

	/**
	 *
	 * @param closure $callback
	 */
	public function notify($callback)
	{
		Args::callBackArgs($callback, 1, 'callback');

		foreach($this->listeners as $listener)
		{
			$callback($listener);
		}
	}

	/**
	 * @param $listener The listener to validate
	 * @return Boolean true if the listener is valid
	 */
	protected abstract function validateListener($listener);
}

?>
