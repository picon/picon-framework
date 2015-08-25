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

namespace picon\web\listeners\component\authorisation;

use picon\core\PiconApplication;
use picon\web\Component;
use picon\web\listeners\component\ComponentInstantiationListener;

/**
 * A component instantiation listener that detects the users authorisation
 * to use a component. Passes the component onto the authorisation strategry
 * which will return a boolen to confirm authorised or not.
 * 
 * @author Martin Cassidy
 * @package web/listeners/component/authorisation
 */
class ComponentAuthorisationListener implements ComponentInstantiationListener
{
    public function onInstantiate(Component &$component)
    {
        $strategy = PiconApplication::get()->getSecuritySettings()->getAuthorisationStrategy();
        
        if(!$strategy->isComponentInstantiationAuthorised($component))
        {
            PiconApplication::get()->getSecuritySettings()->getComponentNotAuthorisedListener()->onNotAuthorised($component);
        }
    }
}

?>
