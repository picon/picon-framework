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
 * Configuration domain object
 *
 * @author Martin Cassidy
 * @package domain/config
 */
class Config extends ComonDomainBase
{
    private $homePage;
    private $mode;
    private $dataSources = array();
    
    public function setMode(ApplicationMode $mode)
    {
        $this->mode = $mode;
    }
    
    public function getMode()
    {
        return $this->mode;
    }
    
    public function setHomePage($homePage)
    {
        $this->homePage = $homePage;
    }
    
    public function getHomePage()
    {
        return $this->homePage;
    }
    
    public function addDataSource(DataSourceConfig $source)
    {
        array_push($this->dataSources, $source);
    }
    
    public function getDataSources()
    {
        return $this->dataSources;
    }
}

?>
