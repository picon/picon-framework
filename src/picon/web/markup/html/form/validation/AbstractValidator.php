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
 * Super class for all validators
 * TODO validators should not use string based messages but
 * should instead use a resource key to locate a message from a file
 * @author Martin Cassidy
 * @package web/markup/html/form/validation
 */
abstract class AbstractValidator implements Validator
{
	public function validate(Validatable $validateable)
	{
		if($validateable->isValid())
		{
			$response = $this->validateValue($validateable);

			if($response!=null && $response instanceof ValidationResponse)
			{
				$validateable->error($response);
			}
		}
	}

	public abstract function validateValue(Validatable $validateable);

	protected final function getKeyName($class = null)
	{
		if($class==null)
		{
			$class = get_called_class();
		}
		$reflection = new \ReflectionClass($class);
		return $reflection->getShortName();
	}
}

?>
