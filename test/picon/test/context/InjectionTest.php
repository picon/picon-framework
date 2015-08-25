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

namespace picon\test\context;

use picon\context\Injector;
use picon\test\app\EmptyInjectable;
use picon\test\app\InvalidInjectable;
use picon\test\app\InvalidNameInjectable;
use picon\test\core\AbstractPiconUnitTest;

class InjectionTest extends AbstractPiconUnitTest
{
    public function testInjector()
    {
        $context = $this->getContext();
        $injector = new Injector($context);
        
        foreach($context->getResources() as $resource)
        {
            $injector->inject($resource);
        }
        
        $this->assertInstanceOf('picon\test\app\TestService', $context->getResource("testRepository")->getTestService());
        $this->assertInstanceOf('picon\test\app\TestRepositoryName', $context->getResource("testRepository")->getTestRepo());
        $this->assertInstanceOf('picon\test\app\TestServiceName', $context->getResource("testRepository")->getTestServ());
        
        
        $this->assertInstanceOf('picon\test\app\TestRepository', $context->getResource("testService")->getTestRepository());
        $this->assertInstanceOf('picon\test\app\TestRepositoryName', $context->getResource("testService")->getTestRepo());
        $this->assertInstanceOf('picon\test\app\TestServiceName', $context->getResource("testService")->getTestServ());
        
        $this->assertInstanceOf('picon\test\app\TestRepository', $context->getResource("repo")->getTestRepository());
        $this->assertInstanceOf('picon\test\app\TestService', $context->getResource("repo")->getTestService());
        $this->assertInstanceOf('picon\test\app\TestServiceName', $context->getResource("repo")->getTestServ());
        
        $this->assertInstanceOf('picon\test\app\TestRepository', $context->getResource("serv")->getTestRepository());
        $this->assertInstanceOf('picon\test\app\TestService', $context->getResource("serv")->getTestService());
        $this->assertInstanceOf('picon\test\app\TestRepositoryName', $context->getResource("serv")->getTestRepo());
    }
    
    public function testSeperateInjector()
    {
        $empty = new EmptyInjectable();
        $injector = new Injector($this->getContext());
        
        $injector->inject($empty);
        
        $this->assertInstanceOf('picon\test\app\TestService', $empty->getTestService());
        $this->assertInstanceOf('picon\test\app\TestRepository', $empty->getTestRepository());
        $this->assertInstanceOf('picon\test\app\TestRepositoryName', $empty->getTestRepo());
        $this->assertInstanceOf('picon\test\app\TestServiceName', $empty->getTestServ());
    }
    
    public function testSeperateInjectorAlias()
    {
        $empty = new EmptyInjectable();
        $injector = new Injector($this->getContext());
        
        $injector->inject($empty);
        
        $this->assertInstanceOf('picon\test\app\TestService', $empty->getTestService());
        $this->assertInstanceOf('picon\test\app\TestRepository', $empty->getTestRepository());
        $this->assertInstanceOf('picon\test\app\TestRepositoryName', $empty->getTestRepo());
        $this->assertInstanceOf('picon\test\app\TestServiceName', $empty->getTestServ());
    }
    
   /**
    * @expectedException picon\core\exceptions\UndefinedResourceException
    */
    public function testInvalidResource()
    {
        $toInject = new InvalidInjectable();
        $injector = new Injector($this->getContext());
        
        $injector->inject($toInject);
    }
    
   /**
    * @expectedException picon\core\exceptions\UndefinedResourceException
    */
    public function testInvalidResourceAlias()
    {
        $toInject = new InvalidNameInjectable();
        $injector = new Injector($this->getContext());
        
        $injector->inject($toInject);
    }
}
?>
