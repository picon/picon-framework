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
 * 
 * Repository path:    $HeadURL$
 * Last committed:     $Revision$
 * Last changed by:    $Author$
 * Last changed date:  $Date$
 * ID:                 $Id$
 * 
 * */

use picon\web\Label;
use picon\web\FeedbackPanel;
use picon\web\markup\html\form\Form;
use picon\web\RequiredTextField;
use picon\web\PropertyModel;
use picon\web\ajax\markup\html\AjaxButton;
use picon\web\AjaxRequestTarget;

/**
 * Description of AjaxLinkPage
 * 
 * @author Martin Cassidy
 */
class AjaxButtonPage extends AbstractPage
{
    private $text = 'Default text';
    
    public function __construct()
    {
        parent::__construct();
        
        $feedback = new FeedbackPanel('feedback');
        $feedback->setOutputMarkupId(true);
        $this->add($feedback);
        
        $label = new Label('text', new PropertyModel($this, 'text'));
        $label->setOutputMarkupId(true);
        $this->add($label);
        
        $form = new Form('form');
        $this->add($form);
        $form->add(new RequiredTextField('text', new PropertyModel($this, 'text')));
        
        $self = $this;
        $form->add(new AjaxButton('button', function(AjaxRequestTarget $target) use ($label, $feedback)
        {
            $target->add($label);
            $target->add($feedback);
        }, 
        function(AjaxRequestTarget $target) use ($feedback)
        {
            $target->add($feedback);
        }));
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/ajax/AjaxButtonPage.php', 'assets/ajax/AjaxButtonPage.html');
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
