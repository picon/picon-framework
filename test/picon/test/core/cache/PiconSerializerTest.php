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

namespace picon\test\core\cache;

use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use picon\context\AutoContextLoader;
use picon\context\Injector;
use picon\core\cache\PiconSerializer;
use picon\core\ConfigLoader;
use picon\test\core\AbstractPiconUnitTest;
use picon\test\core\cache\serializables\InjectOnWakeup;

class PiconSerializerTest extends AbstractPiconUnitTest
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
        $complex = new serializables\ComplexSerialize();
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
        
        $object = new serializables\SimpleArrayObject();
        $object->setArray($simpleArray);
        $arrayObject = $this->doSerialize($object);
        $this->assertArray($simpleArray, $arrayObject->getArray());
        
        $simpleObjectArray = array(new serializables\SimpleSerialize(), new serializables\SimpleSerialize(), new serializables\SimpleSerialize());
        $simpleObjectArrayobject = new serializables\SimpleArrayObject();
        $simpleObjectArrayobject->setArray($simpleObjectArray);
        $arrayObject = $this->doSerialize($simpleObjectArrayobject);
        
        $self = $this;
        $this->assertArray($simpleObjectArray, $arrayObject->getArray(), function($origional, $new) use($self)
        {
            $self->assertSame($origional->getText(), $new->getText());
        });
        
        $closureArray = array(function($value) 
        {
            return "1".$value;
        }, 
        function($value) 
        {
            return "2".$value;
        }, 
        function($value) 
        {
                return "3".$value;
        });
        $closureArrayobject = new serializables\SimpleArrayObject();
        $closureArrayobject->setArray($closureArray);
        $arrayObject = $this->doSerialize($closureArrayobject);
        $self = $this;
        $this->assertArray($closureArray, $arrayObject->getArray(), function($origional, $new) use($self)
        {
            $value = "something";
            $self->assertSame($origional($value), $origional($value));
        });
    }
    
    public function testRestorePoint()
    {
        $complex = new serializables\ComplexSerialize();
        $this->doSerialize($complex);
        
        $closure = $complex->getClosure();
        $this->assertTrue(is_callable($closure));
        $output = $closure();
        $this->assertSame("executing 12", $output);
        
        $this->assertSame("newValue", $complex->getTransient());
        
        //@todo test array restores
    }
    
    public function testWakeupInjection()
    {
        $context = $this->getContext();
        $injector = new Injector($context);
        $object = new InjectOnWakeup();
        $injector->inject($object);
        $deserialized = $this->doSerialize($object);
        
        $this->assertSame($context->getResource("testService"), $object->getTestResource());
        $this->assertSame($context->getResource("testService"), $deserialized->getTestResource());
    }
    
    public function testDetachable()
    {
        $detachable = new serializables\DetachableObject();
        $unserialized = $this->doSerialize($detachable);
        $this->assertSame($detachable->getText(), "after");
    }
    
    public function testObjectRecursion()
    {
        $complex = new serializables\ComplexSerialize();
        $complex->setObject($complex);
        $deSerialized = $this->doSerialize($complex);
        
        $this->assertSame($deSerialized, $deSerialized->getObject());
        
        $secondComplex = new serializables\ComplexSerialize();
        $complex->setObject($secondComplex);
        $secondComplex->setObject($complex);
        $deSerialized = $this->doSerialize($complex);
        $this->assertSame($deSerialized, $deSerialized->getObject()->getObject());
    }
    
    /**
     * @todo test for fully recursive arrays within arrays when the serializer supports
     */
    public function testArrayRecursion()
    {
        $simpleArray = array(array("value1", "value2", "value3"), array("value1", "value2", "value3"), array("value1", "value2", "value3"));
        
        $object = new serializables\SimpleArrayObject();
        $object->setArray($simpleArray);
        $arrayObject = $this->doSerialize($object);
        $this->assertArray($simpleArray, $arrayObject->getArray());
        
        $arrays = $arrayObject->getArray();
        for($i = 0; $i < count($simpleArray); $i++)
        {
            $this->assertArray($simpleArray[$i], $arrays[$i]);
        }
    }
    
    private function doSerialize($object)
    {
        $serialized = PiconSerializer::serialize($object);
        return PiconSerializer::unserialize($serialized);
    }

    /**
     * @param $origionalArray
     * @param $newArray
     * @param callable $eachCallback
     */
    private function assertArray($origionalArray, $newArray, $eachCallback = null)
    {
        $this->assertCount(count($origionalArray), $newArray);
        
        for($i = 0; $i < count($origionalArray); $i++)
        {
            if($eachCallback==null)
            {
                $this->assertSame($origionalArray[$i], $newArray[$i]);
            }
            else
            {
                $eachCallback($origionalArray[$i], $newArray[$i]);
            }
        }
    }
}

?>
