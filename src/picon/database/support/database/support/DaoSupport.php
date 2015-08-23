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

namespacepicon\database\support;

use picon\beans\InitializingBean;
use picon\core\exceptions\IllegalStateException;
use picon\database\source\DataSource;
use picon\database\template\DataBaseTemplate;

/**
 * Super class for any user defined DAO
 * Provides easy access to the database template and allows the DAO to
 * specify which data source to use by implementing init()
 * 
 * @author Martin Cassidy
 * @package database/support
 */
abstract class DaoSupport implements InitializingBean
{
    private $template;
    
    public function setTemplate(DataBaseTemplate $template)
    {
        $this->template = $template;
    }
    
    public function getTemplate()
    {
        return $this->template;
    }
    
    public function setDataSource(DataSource $source)
    {
        $this->template = new DataBaseTemplate($source);
    }
    
    public function getDataSource()
    {
        return $this->template->getDataSource();
    }
    
    public final function afterPropertiesSet()
    {
        $this->init();
        
        if($this->template==null)
        {
            throw new IllegalStateException('The database template was null after init()');
        }
    }
    
    /**
     * Called when the Dao is ready to be initialized
     * Sub classes will need to ensure that the implementation of this method will set the
     * data source or the template
     */
    protected abstract function init();
}

?>
