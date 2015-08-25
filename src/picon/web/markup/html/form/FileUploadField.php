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

namespace picon\web\markup\html\form;
use picon\web\domain\ComponentTag;
use picon\web\model\FileModel;

/**
 * A form field for uploading files
 * 
 * @author Martin Cassidy
 */
class FileUploadField extends FormComponent
{
    public function __construct($id, FileModel $model = null)
    {
        parent::__construct($id, $model);
    }
    
    protected function onComponentTag(ComponentTag $tag)
    {
        parent::onComponentTag($tag);
        $this->checkComponentTag($tag, 'input');
        $this->checkComponentTagAttribute($tag, 'type', 'file');
    }
    
    protected function convertInput()
    {
        $this->setConvertedInput($_FILES[$this->getName()]);
    }
    
    protected function validateModel()
    {
        if(!($this->getModel() instanceof FileModel))
        {
            throw new \picon\core\exceptions\IllegalStateException(sprintf("A file upload field must have a file model, actual %s", gettype($this->getModelObject())));
        }
    }
    
    public function isMultiPart()
    {
        return true;
    }
}

?>
