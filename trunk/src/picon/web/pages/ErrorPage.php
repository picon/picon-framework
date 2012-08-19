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
use picon\Label;
use picon\ListView;
use picon\ArrayModel;
use picon\BasicModel;
use picon\MarkupContainer;

/**
 * Generic page for showing an exception
 * @author Martin Cassidy
 * @package web/pages
 */
class ErrorPage extends WebPage
{
	public function __construct(\Exception $ex)
	{
		$this->add(new Label('title', new BasicModel(get_class($ex))));
		$this->add(new Label('message', new BasicModel($ex->getMessage())));

		$this->add(new ListView('stack', function(MarkupContainer $entry)
		{
			$object = $entry->getModel()->getModelObject();
			$entry->add(new Label('class', new BasicModel(array_key_exists('class', $object)?$object['class']:'')));
			$entry->add(new Label('function', new BasicModel(array_key_exists('function', $object)?$object['function']:'')));
			$entry->add(new Label('file', new BasicModel(array_key_exists('file', $object)?$object['file']:'')));
			$entry->add(new Label('line', new BasicModel(array_key_exists('line', $object)?$object['line']:'')));
		}, new ArrayModel($ex->getTrace())));
	}
}

?>
