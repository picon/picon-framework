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

namespace picon\test\core\utils;

use picon\core\utils\XMLParser;
use picon\test\core\AbstractPiconUnitTest;


/**
 * Tests for the Xml Parser
 * 
 * @author Martin Cassidy
 */
class XmlParserTest extends AbstractPiconUnitTest
{
    public function testValidXML()
    {
        $parser = new XMLParser();
        $output = $parser->parse(dirname(__FILE__).'/validxml.xml');
        
        $this->assertTrue(count($output)==1);
        $this->assertSame("validXml", $output->getName());
        
        $this->assertEquals(1, count($output->getChildren()));
        
        $children = $output->getChildren();
        
        $this->assertSame("someElement", $children[0]->getName());
        $this->assertSame("somedata", $children[0]->getCharacterData());
        
        $attributes = $children[0]->getAttributes();
        $this->assertTrue(count($attributes)==2);
        $this->assertArrayHasKey("attribute1", $attributes);
        $this->assertArrayHasKey("attribute1", $attributes);
        $this->assertSame("value1", $attributes['attribute1']);
        $this->assertSame("value2", $attributes['attribute2']);
    }
    
   /**
    * @expectedException picon\core\exceptions\XMLException
    */
    public function testBadXml()
    {
        $parser = new XMLParser();
        $parser->parse(dirname(__FILE__).'/badxml.xml');
    }
    
   /**
    * @expectedException picon\core\exceptions\FileException
    */
    public function testNoFile()
    {
        $parser = new XMLParser();
        $parser->parse('doesnotexist.xml');
    }
}

?>
