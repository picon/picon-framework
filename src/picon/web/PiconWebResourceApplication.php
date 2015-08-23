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
 * $HeadURL$
 * $Revision$
 * $Author$
 * $Date$
 * $Id$
 */

namespace picon\web;

use picon\core\PiconApplication;
use picon\web\application\WebResourceApplicationInitializer;
use picon\web\request\resolver\ResourceRequestResolver;
use picon\web\request\target\ExceptionPageRequestTarget;
use picon\web\request\WebRequest;
use picon\web\request\WebResponse;


/**
 * A Picon Application for generating resources used within a web pages.
 * This is typically CSS or JavaScript.
 * 
 * This is a skimmed down version of an application and does not load
 * the config or context.
 *
 * @author Martin Cassidy
 */
class PiconWebResourceApplication extends PiconApplication
{
    protected function getApplicationInitializer()
    {
        return new WebResourceApplicationInitializer();
    }
    
    public function run()
    {
        $request = new WebRequest();
        $response = new WebResponse;
        $resolver = new ResourceRequestResolver();
        $target = null;
        if($resolver->matches($request))
        {
            $target = $resolver->resolve($request);
        }
        else
        {
            $target = new ExceptionPageRequestTarget(new \picon\core\exceptions\UnsupportedOperationException("Resource application can only handle a resource request"));
        }
        $target->respond($response);
    }
    
    public function __destruct()
    {
        ob_end_flush();
    }
}

?>
