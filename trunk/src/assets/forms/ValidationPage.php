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

/**
 * Description of ValidationPage
 * 
 * @author Martin Cassidy
 */
class ValidationPage extends AbstractPage
{
    public function __construct()
    {
        parent::__construct();
        
        $this->add(new \picon\FeedbackPanel('feedback'));
        
        $form = new picon\Form('form');
        $this->add($form);
        
        $self = $this;
        $form->add(new picon\Button('button', function() use($self)
        {
            $self->info('The form validation passed');
        },
        function() use($self)
        {
            $self->info('The form was invalid');
        }));
        
        $email = new picon\RequiredTextField('email');
        $email->add(new picon\EmailAddressValidator());
        $form->add($email);
        
        $maxNumber = new picon\RequiredTextField('maxNumber');
        $maxNumber->add(new \picon\MaximumValidator(14));
        $form->add($maxNumber);
        
        $minNumber = new picon\RequiredTextField('minNumber');
        $minNumber->add(new picon\MinimumValidator(34));
        $form->add($minNumber);
        
        $rangeNumber = new picon\RequiredTextField('rangeNumber');
        $rangeNumber->add(new \picon\RangeValidator(4, 45));
        $form->add($rangeNumber);
        
        $maxString = new picon\RequiredTextField('maxString');
        $maxString->add(new \picon\MaximumLengthValidator(10));
        $form->add($maxString);
        
        $minString = new picon\RequiredTextField('minString');
        $minString->add(new picon\MinimumLengthValidator(4));
        $form->add($minString);
        
        $rangeString = new picon\RequiredTextField('rangeString');
        $rangeString->add(new \picon\RangeLengthValidator(4, 10));
        $form->add($rangeString);
        
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/forms/ValidationPage.php', 'assets/forms/ValidationPage.html');
    }
}

?>
