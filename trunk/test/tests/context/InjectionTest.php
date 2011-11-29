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
class InjectionTest extends AbstractPiconTest
{
    public function testInjector()
    {
        $context = $this->getContext();
        $injector = new \picon\Injector($context);
        
        foreach($context->getResources() as $resource)
        {
            $injector->inject($resource);
        }
        
        $this->assertInstanceOf('TestService', $context->getResource("testRepository")->getTestService());
        $this->assertInstanceOf('TestRepositoryName', $context->getResource("testRepository")->getTestRepo());
        $this->assertInstanceOf('TestServiceName', $context->getResource("testRepository")->getTestServ());
        
        
        $this->assertInstanceOf('TestRepository', $context->getResource("testService")->getTestRepository());
        $this->assertInstanceOf('TestRepositoryName', $context->getResource("testService")->getTestRepo());
        $this->assertInstanceOf('TestServiceName', $context->getResource("testService")->getTestServ());
        
        $this->assertInstanceOf('TestRepository', $context->getResource("repo")->getTestRepository());
        $this->assertInstanceOf('TestService', $context->getResource("repo")->getTestService());
        $this->assertInstanceOf('TestServiceName', $context->getResource("repo")->getTestServ());
        
        $this->assertInstanceOf('TestRepository', $context->getResource("serv")->getTestRepository());
        $this->assertInstanceOf('TestService', $context->getResource("serv")->getTestService());
        $this->assertInstanceOf('TestRepositoryName', $context->getResource("serv")->getTestRepo());
    }
    
    public function testSeperateInjector()
    {
        $empty = new EmptyInjectable();
        $injector = new \picon\Injector($this->getContext());
        
        $injector->inject($empty);
        
        $this->assertInstanceOf('TestService', $empty->getTestService());
        $this->assertInstanceOf('TestRepository', $empty->getTestRepository());
        $this->assertInstanceOf('TestRepositoryName', $empty->getTestRepo());
        $this->assertInstanceOf('TestServiceName', $empty->getTestServ());
    }
    
    public function testSeperateInjectorAlias()
    {
        $empty = new EmptyInjectableName();
        $injector = new \picon\Injector($this->getContext());
        
        $injector->inject($empty);
        
        $this->assertInstanceOf('TestService', $empty->getTestService());
        $this->assertInstanceOf('TestRepository', $empty->getTestRepository());
        $this->assertInstanceOf('TestRepositoryName', $empty->getTestRepo());
        $this->assertInstanceOf('TestServiceName', $empty->getTestServ());
    }
    
   /**
    * @expectedException UndefinedResourceException
    */
    public function testInvalidResource()
    {
        $toInject = new InvalidInjectable();
        $injector = new \picon\Injector($this->getContext());
        
        $injector->inject($toInject);
    }
    
   /**
    * @expectedException UndefinedResourceException
    */
    public function testInvalidResourceAlias()
    {
        $toInject = new InvalidNameInjectable();
        $injector = new \picon\Injector($this->getContext());
        
        $injector->inject($toInject);
    }
}
?>
