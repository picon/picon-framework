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

namespace picon\web;

/**
 * A model which represents a file upload
 * This provides methods to gain access to information about a file upload after
 * it has been uploaded.
 * 
 * @author Martin Cassidy
 */
class FileModel implements Model
{
    private $file;
    
    public function getModelObject()
    {
        return $this->file;
    }
    
    /**
     * The key for a file in $_FILE
     * This method is for internal use only!
     * @param string $object 
     */
    public function setModelObject(&$object)
    {
        $this->file = $object;
    }
    
    public function getName()
    {
        $this->enforce();
        return $this->file['name'];
    }
    
    public function getTempName()
    {
        $this->enforce();
        return $this->file['tmp_name'];
    }
    
    public function getType()
    {
        $this->enforce();
        return $this->file['type'];
    }
    
    public function getSize()
    {
        $this->enforce();
        return $this->file['size'];
    }
    
    public function getError()
    {
        $this->enforce();
        return $this->file['error'];
    }
    
    private function enforce()
    {
        if($this->file==null)
        {
            throw new \picon\core\exceptions\IllegalStateException('No file upload has been registered with this model');
        }
    }
}

?>
