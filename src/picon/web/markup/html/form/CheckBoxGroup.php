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
 * A wrapping component for checks
 *
 * @see Check
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class CheckBoxGroup extends FormComponent
{
	protected function onInitialize()
	{
		parent::onInitialize();
		$this->validateModel();
	}

	protected function validateModel()
	{
		$object = $this->getModelObject();
		if($object!=null && !is_array($object))
		{
			throw new \IllegalStateException('Check box group must have an array model');
		}
	}

	public function getChoiceGroup()
	{
		$choice = null;
		$callback = function(&$component) use (&$choice)
		{
			$choice = $component;
			return Component::VISITOR_STOP_TRAVERSAL;
		};
		$this->visitParents(Identifier::forName('picon\ChoiceGroup'), $callback);
		return $choice;
	}

	public function getName()
	{
		$choice = $this->getChoiceGroup();
		if($choice==null)
		{
			return parent::getName();
		}
		else
		{
			return str_replace('.', '_', $choice->getComponentPath());
		}
	}

	protected function convertInput()
	{
		$checks = array();
		$callback = function(&$component) use(&$checks)
		{
			array_push($checks, $component);
			return Component::VISITOR_CONTINUE_TRAVERSAL;
		};
		$this->visitChildren(Check::getIdentifier(), $callback);
		$values = array();

		foreach($checks as $check)
		{
			if(in_array($check->getValue(), $this->getRawInputArray()))
			{
				array_push($values, $check->getModelObject());
			}
		}
		$this->setConvertedInput($values);
	}

	public function isRequired()
	{
		$choice = $this->getChoiceGroup();
		return $choice==null && parent::isRequired();
	}
}

?>
