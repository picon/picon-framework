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
 * Produces a callback url as the value for the option, the url will resolve
 * to the supplied callback.
 *
 * @author Martin Cassidy
 */
class CallbackOption extends AbstractCallableOption
{
    private $callback;
    
    public function __construct($name, $callback)
    {
        parent::__construct($name);
        Args::callBack($callback, 'callback');
        $this->callback = new SerializableClosure($callback);
    }
    
    public function render(AbstractJQueryBehaviour $behaviour)
    {
        return sprintf("%s : '%s'", $this->getName(), $this->getUrl($behaviour));
    }
    
    /**
     * Despite acception the ajax request target, this option is for passing only 
     * the url, thus the ajax request will be internally within a jQuery plugin
     * and will bypass the picon ajax javascript. This means the response is
     * not needed.
     * @param AjaxRequestTarget $target
     */
    public function call(AjaxRequestTarget $target)
    {
        $callable = $this->callback;
        $callable();
    }
}

?>
