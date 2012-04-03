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

use picon\Request;

/**
 * Description of AbstractRequestResolverTest
 * @author Martin Cassidy
 */
abstract class AbstractRequestResolverTest extends AbstractPiconTest
{
    private $resolver;
    
    protected function setUp()
    {
        $this->resolver = $this->newResolver();
    }
    
    public function testHomePage()
    {
        //Homepage request
        $testRequest = new TestRequest(TestRequest::ROOT_PATH.'/', array());
        $this->assertEquals($this->matchesHomePage($testRequest), $this->resolver->matches($testRequest));
        
        if($this->matchesHomePage($testRequest))
        {
            //$this->assertTrue($this->resolver->resolve($testRequest) instanceof PageRequestTarget);
        }
        
        //Homepage with listener request
        $testRequest = new TestRequest(TestRequest::ROOT_PATH.'/', array('listener' => 'someListenerPath'));
        $this->assertEquals($this->matchesHomePage($testRequest), $this->resolver->matches($testRequest));
        
        if($this->matchesHomePage($testRequest))
        {
            //$this->assertTrue($this->resolver->resolve($testRequest) instanceof picon\ListenerRequestTarget);
        }
    }
    
    public function testStatelessPage()
    {
        
    }
    
    protected abstract function newResolver();
    
    protected function matchesHomePageListener(Request $request)
    {
        return false;
    }
    
    protected function matchesHomePage(Request $request)
    {
        return false;
    }
}

?>
