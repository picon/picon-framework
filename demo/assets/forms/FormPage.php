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

use picon\web\AjaxButton;
use picon\web\BasicModel;
use picon\web\Button;
use picon\web\Check;
use picon\web\CheckBox;
use picon\web\CheckBoxGroup;
use picon\web\CheckChoice;
use picon\web\CompoundPropertyModel;
use picon\web\DropDown;
use picon\web\FeedbackPanel;
use picon\web\Label;
use picon\web\ListMultiple;
use picon\web\ListView;
use picon\web\markup\html\form\Form;
use picon\web\MarkupContainer;
use picon\web\Radio;
use picon\web\RadioChoice;
use picon\web\RadioGroup;
use picon\web\TextArea;
use picon\web\TextField;

/**
 * Description of FormPage
 * 
 * @author Martin Cassidy
 */
class FormPage extends AbstractPage
{
    private $domain;
    
    public function __construct()
    {
        parent::__construct();
        $this->domain = new ExampleDomain();
        $this->setModel(new CompoundPropertyModel($this, 'domain'));
        $feedback = new FeedbackPanel('feedback');
        $this->add($feedback);
        
        $form = new Form('form');
        $this->add($form);
        $self = $this;
        $form->add(new Button('button', function() use($self)
        {
            $self->info('First button pressed, valid form');
        },
        function() use($self)
        {
            $self->info('First button pressed, invalid form');
        }));
        
        $form->add(new Button('button2', function() use($self)
        {
            $self->info('Second button pressed, valid form');
        },
        function() use($self)
        {
            $self->info('Second button pressed, invalid form');
        }));
        
        $submitedInfo = new MarkupContainer('submitedInfo');
        $this->add($submitedInfo);
        
        $choices = array('default', 'option', 'other option', 'something else');
        $form->add(new TextField('textBox'));
        $form->add(new TextArea('textArea'));
        $form->add(new DropDown('select', $choices));
        
        $group = new RadioGroup('rgroup');
        $form->add($group);
        
        $group->add(new Radio('gradio1', new BasicModel('option1')));
        $group->add(new Radio('gradio2', new BasicModel('option2')));
        
        $form->add(new CheckBox('icheck'));
        
        $checkGroup = new CheckBoxGroup('cgroup');
        $form->add($checkGroup);
        $checkGroup->add(new Check('check1', new BasicModel('option1')));
        $checkGroup->add(new Check('check2', new BasicModel('option2')));
        
        $form->add(new RadioChoice('rchoice', $choices));
        $form->add(new CheckChoice('cchoice', $choices));
        
        $form->add(new ListMultiple('mchoice', $choices));
        
        $submitedInfo->add(new Label('textBox'));
        $submitedInfo->add(new Label('textArea'));
        $submitedInfo->add(new Label('select'));
        $submitedInfo->add(new Label('rgroup'));
        $submitedInfo->add(new Label('icheck'));
        
        $submitedInfo->add(new ListView('cgroup', function(&$item)
        {
            $item->add(new Label('value', $item->getModel()));
        }));
        
        $submitedInfo->add(new Label('rchoice'));
        
        $submitedInfo->add(new ListView('cchoice', function(&$item)
        {
            $item->add(new Label('value', $item->getModel()));
        }));
        
        $submitedInfo->add(new ListView('mchoice', function(&$item)
        {
            $item->add(new Label('value', $item->getModel()));
        }));
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/forms/FormPage.php', 'assets/forms/FormPage.html');
    }
}

?>
