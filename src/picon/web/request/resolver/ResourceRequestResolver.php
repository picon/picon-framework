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
 * Resolver for resource requests such as css and js
 * 
 * @todo resources should be generated and cached under an unique id, this should be part of
 * the resource request which should go through resource.php (to be created) not index.php to save
 * application start up for resources
 * @author Martin Cassidy
 * @package web/request/resolver
 */
class ResourceRequestResolver implements RequestResolver
{
    /**
     * @todo validate the content of the param with a reg ex, return false if
     * it doesn't match
     * @param Request $request
     * @return type 
     */
    public function matches(Request $request)
    {
        return $request->isResourceRequest();
    }
    
    public function resolve(Request $request)
    {
        $resourceString = $request->getParameter('picon-resource');
        $resourceArray = explode(':', $resourceString);
        $identifier = Identifier::forName(str_replace('.', '\\', $resourceArray[0]));
        $file = $resourceArray[1];
        $resource = new ResourceReference($file, $identifier);
        return new ResourceRequestTarget($resource);
    }
    
    public function generateUrl(RequestTarget $target)
    {
        $file = $target->getResource()->getFile();
        $identifier = $target->getResource()->getIdentifier();
        $fqName = $identifier->getFullyQualifiedName();
        $fqName = str_replace('\\', '.', $fqName);
        return '?picon-resource='.$fqName.':'.$file;
    }
    
    public function handles(RequestTarget $target)
    {
        return $target instanceof ResourceRequestTarget;
    }
}

?>
