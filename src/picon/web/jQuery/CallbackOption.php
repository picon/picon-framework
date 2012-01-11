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
 * Description of CallbackOption
 *
 * @author Martin Cassidy
 */
class CallbackOption extends AbstractOption
{
    private $callback;
    
    public function __construct($name, $callback)
    {
        parent::__construct($name);
        Args::callBackArgs($callback, 1, 'callback');
        $this->callback = new SerializableClosure($callback);
    }
    
    public function render(AbstractJQueryBehaviour $behaviour)
    {
        $url = $behaviour->getComponent()->generateUrlFor($behaviour);
        return sprintf("%s : function() {piconAjaxGet('%s&ajax=ajax&property=%s', function(){}, function(){});}", $this->getName(), $url, $this->getName());
    }
    
    public function call(AjaxRequestTarget $target)
    {
        $callable = $this->callback;
        $callable($target);
    }
}

?>
