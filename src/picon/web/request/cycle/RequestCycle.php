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

namespace picon\web;

use picon\PiconApplication;

/**
 * Front controller for incoming requests.
 * The represents the process of handling a request in the following steps:
 * <ol>
 * <li>Resolves the request to a target</li>
 * <li>Executes any listener events</li>
 * <li>Produces the response</li>
 * </ol>
 * 
 * @author Martin Cassidy
 * @package web/request/cycle
 * @todo create request listeners
 */
class RequestCycle
{
    private $request;
    private $response;
    private $resolver;
    private $targetStack;
    private $maxStackSize = 10; //@todo put this somewhere else
    
    public function __construct()
    {
        $GLOBALS['requestCycle'] = $this;
        $this->targetStack = new \ArrayObject();
        $this->resolver = new RequestResolverCollection();
        $this->request = new WebRequest();
        $this->response = new WebResponse();
    }
    
    public function process()
    {
        $target = $this->resolver->resolve($this->request);
        
        if($target!=null)
        {
            $this->addTarget($target);
        }
        
        if(count($this->targetStack)==0)
        {
            $this->addTarget(new PageNotFoundRequestTarget());
        }
        

        $iterator = $this->targetStack->getIterator();

        while($iterator->valid()) 
        {
            try
            {
                if(PiconApplication::get()->getProfile()->isCleanBeforeOutput())
                {
                    ob_clean();
                    $this->response->clean();
                }
                $iterator->current()->respond($this->response);
            }
            catch(RestartRequestOnPageException $restartEx)
            {
                $this->targetStack->exchangeArray(array());
                $this->addTarget(new PageRequestTarget($restartEx->getPageIdentifier()));
                $iterator->rewind();
            }
            catch(\Exception $ex)
            {
                $this->targetStack->exchangeArray(array());
                $this->addTarget(new ExceptionPageRequestTarget($ex));
                $iterator->rewind();
            }
            $iterator->next();
        }
        
    }
    
    public function addTarget($target)
    {
        if($target==null)
        {
            return;
        }
        if(!($target instanceof RequestTarget))
        {
            throw new \InvalidArgumentException("addTarget() expects a paramater that is an instance of RequestTarget");
        }
        if($this->targetStack->count()==$this->maxStackSize)
        {
            throw new \OverflowException(sprintf("The request target stack cannot contain more than %d elements per request", $this->maxStackSize));
        }
        $this->targetStack->append($target);
    }
    
    public function generateUrl(RequestTarget $target)
    {
        return $this->resolver->generateUrl($target);
    }
    
    public function getResponse()
    {
        return $this->response;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public static function get()
    {
        return $GLOBALS['requestCycle'];
    }
    
    public function containsTarget(Identifier $contains)
    {
        foreach($this->targetStack as $target)
        {
            $identifier = Identifier::forObject($target);
            if($identifier->of($contains))
            {
                return true;
            }
        }
        return false;
    }
}

?>
