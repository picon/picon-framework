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

namespace picon\web\ajax;

use picon\Args;

/**
 * An ajax decorator that invokeds a callback function for each of the decorator
 * options.
 *
 * @author Martin Cassidy
 * @package web/ajax
 */
class CallbackAjaxCallDecorator implements AjaxCallDecorator
{
    private $decorator;
    private $successDecorator;
    private $failDecorator;
    
    /**
     *
     * @param callable $decorator
     * @param callable $successDecorator
     * @param callable $failDecorator
     */
    public function __construct($decorator, $successDecorator = null, $failDecorator = null)
    {
        Args::callBackArgs($decorator, 1, 'decorator');
        
        if($successDecorator!=null)
        {
            Args::callBackArgs($successDecorator, 1, 'successDecorator');
        }
        
        if($failDecorator!=null)
        {
            Args::callBackArgs($failDecorator, 1, 'failDecorator');
        }
        
        $this->decorator = $decorator;
        $this->successDecorator = $successDecorator;
        $this->failDecorator = $failDecorator;
    }
    
    public function decorateScript($script)
    {
        $callable = $this->decorator;
        return $callable($script);
    }
    
    public function decorateSuccessScript($script)
    {
        $callable = $this->successDecorator;
        if($callable==null) {
            return $script;
        }
        return $callable($script);
    }
    
    public function decorateFailScript($script)
    {
        $callable = $this->failDecorator;
        if($callable==null)
        {
            return $script;
        }
        return $callable($script);
    }
}

?>
