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
 * A list of radio buttons generated from an array of choices.
 *
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class RadioChoice extends AbstractSingleChoice implements ChoiceGroup
{
	private $selection;

	/**
	 *
	 * @param string $id
	 * @param array $choices
	 * @param Model $model
	 */
	public function __construct($id, $choices, Model $model = null)
	{
		parent::__construct($id, $choices, $model);
	}

	protected function onInitialize()
	{
		parent::onInitialize();
		$this->selection = $this->getModelObject();
		$this->group = new RadioGroup('choice', new PropertyModel($this, 'selection'));
		$this->add($this->group);
		//TODO add the type hint b/ack into the closure when the serializer can handle them
		$this->group->add(new ListView('choices', function(&$item)
		{
			$radio = new \picon\Radio('radio', $item->getModel());
			$item->add($radio);
			$item->add(new \picon\FormComponentLabel('label', $radio));
		}, new ArrayModel($this->getChoices())));
	}

	protected function newMarkupSource()
	{
		return new PanelMarkupSource();
	}

	public function __get($name)
	{
		return $this->$name;
	}

	public function __set($name, $value)
	{
		$this->$name = $value;
	}


	protected function convertInput()
	{
		$this->group->processInput();
		$this->setConvertedInput($this->group->getConvertedInput());
	}
}

?>
