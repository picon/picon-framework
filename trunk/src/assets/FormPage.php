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
use picon\Form;
use picon\Button;
use picon\TextField;
use picon\CompoundPropertyModel;
use picon\Label;
use picon\TextArea;
use picon\DropDown;
use picon\Radio;
use picon\RadioGroup;
use picon\CheckBox;
use picon\Check;
use picon\CheckBoxGroup;
use picon\CheckChoice;
use picon\RadioChoice;
use picon\BasicModel;
use picon\ListView;
use picon\ListItem;
use picon\FeedbackPanel;
use picon\EmailAddressValidator;

/**
 * Description of FormPage
 * 
 * @author Martin Cassidy
 */
class FormPage extends WebPage
{
    private $domain;
    
    public function __construct()
    {
        parent::__construct();
        $this->domain = new ExampleDomain();
        $this->setModel(new CompoundPropertyModel($this, 'domain'));
        
        $this->add(new FeedbackPanel('feedback'));
        
        $this->info('Sample feedback message. These do not persist from request to request');
        
        $form = new Form('form');
        $this->add($form);
        $form->add(new Button('button', function()
        {
            
        }));
        
        $choices = array('default', 'option', 'other option', 'something else');
        $text = new TextField('textBox');
        $text->setRequired(true);
        $text->add(new EmailAddressValidator());
        $form->add($text);
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
        
        $this->add(new Label('textBox'));
        $this->add(new Label('textArea'));
        $this->add(new Label('select'));
        $this->add(new Label('rgroup'));
        $this->add(new Label('icheck'));
        
        $this->add(new ListView('cgroup', function(&$item)
        {
            $item->add(new \picon\Label('value', $item->getModel()));
        }));
        
        $this->add(new Label('rchoice'));
        
        $this->add(new ListView('cchoice', function(&$item)
        {
            $item->add(new \picon\Label('value', $item->getModel()));
        }));
    }
    
    public function isPageStateless()
    {
        return false;
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}

?>
