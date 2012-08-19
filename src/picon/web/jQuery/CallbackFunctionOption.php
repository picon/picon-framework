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
 * Allows a user specified function to be rendered but wraps with a callback in the following way
 *
 * [optionName] : function([args...])
 * {
 *      var callBackURL = '[url]';
 *      [userFucntionCode]
 *      piconAjaxGet(callBackURL, function(){}, function(){});
 * }
 *
 * This allows the callback url to be altered by user defined code with function arguments before
 * it is sent
 *
 * @author Martin Cassidy
 * @package web/jQuery
 */
class CallbackFunctionOption extends AbstractCallableOption
{
	private $function;
	private $args = array();
	private $callback;

	/**
	 *
	 * @param type $name
	 * @param type $function
	 * @param ... args the names of the arguments the javascript function should take
	 */
	public function __construct($name, $callback, $function)
	{
		parent::__construct($name);
		Args::isString($function, 'function');
		Args::callBackArgs($callback, 1, 'callback');
		$this->callback = new SerializableClosure($callback);
		$this->function = $function;

		$args = func_get_args();
		for($i=3;$i<count($args);$i++)
		{
			array_push($this->args, $args[$i]);
		}
	}

	public function render(AbstractJQueryBehaviour $behaviour)
	{
		return sprintf("%s : function(%s) {var callBackURL = '%s'; %s piconAjaxGet(callBackURL, function(){}, function(){});}", $this->getName(), implode(', ', $this->args), $this->getUrl($behaviour), $this->function);
	}

	public function call(AjaxRequestTarget $target)
	{
		$callable = $this->callback;
		$callable($target);
	}
}

?>
