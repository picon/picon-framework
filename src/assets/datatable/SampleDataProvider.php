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

use picon\DataProvider;

/**
 * Description of SampleDataProvider
 * 
 * @author Martin Cassidy
 */
class SampleDataProvider implements DataProvider
{
    private $records = array();
    
    public function __construct()
    {
        for($i = 0 ; $i < 25; $i++)
        {
            array_push($this->records, new TableEntryDomain('entry '.$i));
        }
    }
    
    public function getSize()
    {
        return count($this->records);
    }
    
    public function getRecords($start, $count)
    {
        return array_slice($this->records, $start, $count);
    }
    
    public function getModel($object)
    {
        return new picon\BasicModel($object);
    }
}

?>
