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
 * Generic page for showing an exception
 * @todo use list view for the trace (create list view!)
 * @author Martin Cassidy
 */
class ErrorPage extends WebPage
{
    public function __construct(\Exception $ex)
    {
        $this->add(new Label('title', new BasicModel(get_class($ex))));
        $this->add(new Label('message', new BasicModel($ex->getMessage())));
        
        $out;
        foreach($ex->getTrace() as $entry)
        {
            $out .= "at $entry[class] $entry[function]() $entry[file] on line $entry[line] <br />";
        }
        
        $this->add(new Label('stack', new BasicModel($out)));
    }
}

?>
