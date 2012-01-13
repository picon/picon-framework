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
 * A pointer to a file which is located in the same directory as the class
 * identifier or one of its super classes
 *
 * @author Martin Cassidy
 * @package web
 */
class ResourceReference
{
    private $file;
    private $identifier;
    
    public function __construct($file, Identifier $identifier)
    {
        Args::isString($file, 'file');
        $this->file = $file;
        $this->identifier = $identifier;
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    public function loadResource()
    {
        $fileName = $this->getResourceFile($this->identifier->getFullyQualifiedName());

        if($fileName==null)
        {
            throw new \RuntimeException(sprintf("Unable to located file for resource %s with class scope %s", $this->file, $this->identifier->getFullyQualifiedName()));
        }
        $resouce = @file_get_contents($fileName);
        
        if($resouce==false)
        {
            throw new \FileException(sprintf("Failed to open file %s", $fileName));
        }
        return $resouce;
    }
    
    private function getResourceFile($className)
    {
        $reflection = new \ReflectionClass($className);
        $fileInfo = new \SplFileInfo($reflection->getFileName());
        
        $file = $fileInfo->getPath()."\\".$this->file;
        if(file_exists($file))
        {
            return $file;
        }
        if(get_parent_class($className)!=false)
        {
            return $this->getResourceFile(get_parent_class($className));
        }
        return null;
    }
}

?>
