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

/**
 * 
 * 
 * @author Martin Cassidy
 */
class SpecialFields extends AbstractPage
{
    public $date;
    public function __construct()
    {
        parent::__construct();
        $feedback = new picon\FeedbackPanel('feedback');
        $this->add($feedback);
        $form = new \picon\Form('form');
        $form->add(new \picon\DateField('date', new \picon\PropertyModel($this, 'date')));
        $feedback->setOutputMarkupId(true);
        $this->add($form);
        $model = new \picon\FileModel();
        $form->add(new \picon\FileUploadField('file', $model));
        
        $self = $this;
        $form->add(new picon\Button('button', function() use ($model,$self)
        {
            $self->success(sprintf("The file was uploaded successfully. Name: %s, size: %d", $model->getName(), $model->getSize()));
            $self->success(sprintf('Date was: %s', $self->date));
        }));
        
        $form->add(new picon\AjaxButton('ajaxbutton', function(picon\AjaxRequestTarget $target) use ($feedback, $model, $self)
        {
            $self->success(sprintf("The file was uploaded successfully. Name: %s, size: %d", $model->getName(), $model->getSize()));
            $self->success(sprintf('Date was: %s', $self->date));
            $target->add($feedback);
        }));
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/SpecialFields.php', 'assets/SpecialFields.html');
    }
}

?>
