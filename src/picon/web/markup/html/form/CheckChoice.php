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
 * An automatically populated list of check boxes to choose from. The options
 * are defined as an array.
 * 
 * @author Martin Cassidy
 * @package web/markup/html/form
 */
class CheckChoice extends AbstractMultipleChoice implements ChoiceGroup
{
    private $group;
    private $selection;
    
    /**
     *
     * @param string $id
     * @param array $choices The available choices
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
        $this->group = new CheckBoxGroup('choice', $this->getModel());
        $this->add($this->group);
        
        //@todo add the type hint back into the closure when the serializer can handle them
        $this->group->add(new ListView('choices', function(&$item)
        {
            $check = new \picon\Check('checkbox', $item->getModel());
            $item->add($check);
            $item->add(new \picon\FormComponentLabel('label', $check));
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
