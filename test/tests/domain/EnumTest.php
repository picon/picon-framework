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
 * Tests the picon implementation of enums
 * 
 * @author Martin Cassidy
 */
class EnumTest extends AbstractPiconTest
{
    public function testEnumValues()
    {
        $one = new TestEnum(TestEnum::ONE);
        $two = new TestEnum(TestEnum::TWO);
        $three = new TestEnum(TestEnum::THREE);
        
        $this->assertSame(TestEnum::ONE, $one->__toString());
        
        $this->assertSame(array('ONE' => TestEnum::ONE, 'TWO' => TestEnum::TWO, 'THREE' => TestEnum::THREE), TestEnum::values());
        
        $this->assertEquals($two, TestEnum::valueOf('two'));
    }
    
    public function testDefaultValues()
    {
        $one = new DefaultTestEnum(TestEnum::ONE);
        $two = new DefaultTestEnum(TestEnum::TWO);
        $three = new DefaultTestEnum(TestEnum::THREE);
        
        $this->assertSame(DefaultTestEnum::ONE, $one->__toString());
        
        $this->assertSame(array('ONE' => DefaultTestEnum::ONE, 'TWO' => DefaultTestEnum::TWO, 'THREE' => DefaultTestEnum::THREE), DefaultTestEnum::values());
        
        $this->assertEquals($two, DefaultTestEnum::valueOf('two'));
        
        $this->assertEquals($three, new DefaultTestEnum);
    }
}

?>
