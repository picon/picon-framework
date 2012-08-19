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
 * A checkbox which may be used independantly, it must have a boolean model
 *
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class CheckBox extends FormComponent
{
	protected function validateModel()
	{
		if($this->getModel()!=null && !($this->getModel() instanceof BooleanModel) && $this->getModelObject()!=null && !is_bool($this->getModelObject()))
		{
			throw new \IllegalStateException(sprintf("A check box must have a boolean model, actual %s", gettype($this->getModelObject())));
		}
	}

	public function getValue()
	{
		return $this->getComponentPath();
	}

	protected function isSelected($value)
	{
		if($this->isEmptyInput())
		{
			return false;
		}
		else
		{
			if($this->getRawInput()==null)
			{
				return $this->getModelObject();
			}
			else
			{
				return $value==$this->getRawInput();
			}
		}
	}

	protected function onComponentTag(ComponentTag $tag)
	{
		parent::onComponentTag($tag);
		$this->checkComponentTag($tag, 'input');
		$this->checkComponentTagAttribute($tag, 'type', 'checkbox');
		$tag->put('value', $this->getValue());

		if($this->isSelected($this->getValue()))
		{
			$tag->put('checked', 'checked');
		}
	}

	protected function convertInput()
	{
		$value = ($this->getRawInput()==$this->getValue())==true;
		$this->setConvertedInput($value);
	}
}

?>
