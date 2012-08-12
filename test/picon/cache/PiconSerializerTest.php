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

class PiconSerializerTest extends AbstractPiconTest
{
    public function testNonObjcts()
    {
        $string = "hello";
        $deSerialized = $this->doSerialize($string);
        $this->assertSame($string, $deSerialized);
        
        $int = 12;
        $deSerialized = $this->doSerialize($int);
        $this->assertEquals($int, $deSerialized);
    }
    
    public function testComplexSerialization()
    {
        $complex = new \ComplexSerialize();
        $deSerialized = $this->doSerialize($complex);
        
        $this->assertSame("newValue", $complex->getTransient());
        $this->assertSame("newValue2", $complex->getService());
        
        $this->assertSame("defaultValue", $deSerialized->getTransient());
        $this->assertSame("defaultValue", $deSerialized->getService());
        
        $closure = $deSerialized->getClosure();
        $this->assertTrue(is_callable($closure));
        $output = $closure();
        $this->assertSame("executing 12", $output);
        
        $this->assertSame("Some text", $deSerialized->getObject()->getText());
        
        $this->assertSame("some text", $deSerialized->getParentString());
    }
    
    public function testObjectWithArrays()
    {
        $simpleArray = array("value1", "value2", "value3");
        
        $object = new \SimpleArrayObject();
        $arrayObject = $this->doSerialize($object);
        $this->assertCount(count($simpleArray), $arrayObject->getArray());
        
        $values = $arrayObject->getArray();
        for($i = 0; $i < count($simpleArray); $i++)
        {
           $this->assertSame($simpleArray[$i], $values[$i]);
        }
    }
    
    public function testRestorePoint()
    {
        
    }
    
    public function testArrays()
    {
        
    }
    
    public function testTransient()
    {
        
    }
    
    public function testWakeupInjection()
    {
        
    }
    
    public function testDetachable()
    {
        
    }
    
    public function testObjectRecursion()
    {
        
    }
    
    public function testArrayRecursion()
    {
        
    }
    
    private function doSerialize($object)
    {
        $serialized = PiconSerializer::serialize($object);
        return PiconSerializer::unserialize($serialized);
    }
}

?>
