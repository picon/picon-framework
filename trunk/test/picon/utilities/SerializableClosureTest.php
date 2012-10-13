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
require_once(dirname(__FILE__).'/../../AbstractPiconTest.php');
require_once(dirname(__FILE__).'/../../testassets/cache/ExternalNamespaceClass.php');

use other\ExternalNamespaceClass;

/**
 *
 * @author Martin Cassidy
 */
class SerializableClosureTest extends AbstractPiconTest
{
    public function testBasicClosure()
    {
        $closure = function()
        {
            return 'test';
        };
        
        $reformed = $this->doSerialize($closure);
        
        $this->assertEquals($closure(), $reformed());
    }
    
    public function testClosureWithArgs()
    {
        $closure = function($value)
        {
            return $value.'test';
        };
        
        $reformed = $this->doSerialize($closure);
        $this->assertEquals($closure('test'), $reformed('test'));
    }
    
    public function testClosureWitClosureBinding()
    {
        $object = new \SimpleSerialize();
        $closure = function() use ($object)
        {
            return $object->getText().'test';
        };
        
        $reformed = $this->doSerialize($closure);
        $this->assertEquals($closure(), $reformed());
    }
    
    public function testClosureWithClosureBinding()
    {
        $object = new \SimpleSerialize();
        $argClosure = function ($value) use ($object)
        {
            return $object->getText().$value.'test';
        };
        
        $closure = function($value) use ($argClosure)
        {
            return $argClosure($value).'test';
        };
        
        $reformed = $this->doSerialize($closure);
        $this->assertEquals($closure('test'), $reformed('test'));
    }
    
    public function testClosureNestedClosure()
    {
        $closure = function()
        {
            return function()
            {
                return 'test';
            };
        };
        $inner = $closure();
        $reformed = $this->doSerialize($closure);
        $reformedInner = $reformed();
        $this->assertEquals($reformedInner(), $inner());
        
        $innerReformed = $this->doSerialize($reformedInner);
        $this->assertEquals($innerReformed(), $inner());
    }
    
    public function testNamespaceFQClassNames()
    {
        $closure = function()
        {
            PiconClass::doSomething();
            return new PiconClass();
        };
        
        $reformed = $this->doSerialize($closure);
        
        $piconClass = $reformed();
        
        $this->assertTrue($piconClass instanceof PiconClass);
    }
    
    public function testUseFQClassNames()
    {
        $closure = function()
        {
            ExternalNamespaceClass::doSomething();
            return new ExternalNamespaceClass();
        };
        
        $reformed = $this->doSerialize($closure);
        
        $externalClass = $reformed();
        
        $this->assertTrue($externalClass instanceof ExternalNamespaceClass);
    }
    
    public function testAlreadyFQClassNames()
    {
        $closure = function()
        {
            \picon\PiconClass::doSomething();
            return new \picon\PiconClass();
        };
        
        $reformed = $this->doSerialize($closure);
        
        $piconClass = $reformed();
        
        $this->assertTrue($piconClass instanceof PiconClass);
    }
    
    public function testTypeHinting()
    {
        $piconClass = new PiconClass();
        $closure = function(PiconClass $typeHintedValue)
        {
            return $typeHintedValue;
        };
        
        $reformed = $this->doSerialize($closure);
        
        $returned = $reformed($piconClass);
        $this->assertTrue($returned instanceof PiconClass);
    }
    
    public function testUseTypeHinting()
    {
        $piconClass = new ExternalNamespaceClass();
        $closure = function(ExternalNamespaceClass $typeHintedValue)
        {
            return $typeHintedValue;
        };
        
        $reformed = $this->doSerialize($closure);
        
        $returned = $reformed($piconClass);
        $this->assertTrue($returned instanceof ExternalNamespaceClass);
    }
    
    public function testAlreadyFQTypeHinting()
    {
        $piconClass = new PiconClass();
        $closure = function(\picon\PiconClass $typeHintedValue)
        {
            return $typeHintedValue;
        };
        
        $reformed = $this->doSerialize($closure);
        
        $returned = $reformed($piconClass);
        $this->assertTrue($returned instanceof PiconClass);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidClosure()
    {
        new SerializableClosure("string");
    }
    
    private function doSerialize($closure)
    {
        $serialized = PiconSerializer::serialize($closure);
        return PiconSerializer::unserialize($serialized);
    }
}

?>
