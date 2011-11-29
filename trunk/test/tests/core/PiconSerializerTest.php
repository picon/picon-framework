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

/**
 * Description of PiconSerializerTest
 * @todo test inject on wakeup
 * @author Martin Cassidy
 */
class PiconSerializerTest extends AbstractPiconTest
{
    public function testComplexSerialization()
    {
        $complexObject = new ComplexSerialize();
        $serialized = serialize($complexObject);
        $deSerialized = unserialize($serialized);
        
        $this->assertSame("defaultValue", $deSerialized->getTransient());
        $this->assertSame("defaultValue", $deSerialized->getService());
        
        $closure = $deSerialized->getClosure();
        $this->assertTrue(is_callable($closure));
        $output = $closure();
        $this->assertSame("executing 12", $output);
    }
    
    //@todo create a test and a process for testing serialization of injected resources
}

?>
