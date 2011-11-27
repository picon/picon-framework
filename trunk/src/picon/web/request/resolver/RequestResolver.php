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
 * Resolver for a request
 * @author Martin Cassidy
 */
interface RequestResolver
{
    /**
     * Resolves a request to a RequestTarget
     * @return RequestTarget the request target for the request
     */
    function resolve(Request $request);
    
    /**
     * 
     * @return boolean true if this request resolver can be used for the request
     */
    function matches(Request $request);
    
    /**
     * Generate a URL for requests of this type
     * @return string the URL for the given target
     */
    function generateUrl(RequestTarget $target);
    
    /**
     * @return boolean true if this resolver can handle the given target
     */
    function handles(RequestTarget $target);
}

?>
