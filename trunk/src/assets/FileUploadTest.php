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
 * Description of FileUploadTest
 * 
 * @author Martin Cassidy
 */
class FileUploadTest extends picon\WebPage
{
    public function __construct()
    {
        parent::__construct();
        $form = new \picon\Form('form');
        $form->setOutputMarkupId(true);
        $this->add($form);
        $model = new \picon\FileModel();
        $form->add(new \picon\FileUploadField('file', $model));
        $form->add(new picon\AjaxButton('button', function(picon\AjaxRequestTarget $target) use ($model, $form)
        {
            $target->executeScript("alert('".$model->getTempName()."');");
            $target->add($form);
        }, function(picon\AjaxRequestTarget $target)
        {
        }));
    }
}

?>
