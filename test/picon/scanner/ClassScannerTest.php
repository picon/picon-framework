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
use picon\core\scanner\ClassNameRule;
use picon\core\scanner\ClassScanner;
use picon\core\scanner\SubClassRule;

require_once(dirname(__FILE__).'/../../AbstractPiconTest.php');

/**
 * Test for the class scanner
 * @todo test multiple rules, test scanning subsets
 * @author Martin Cassidy
 */
class ClassScannerTest extends AbstractPiconTest
{
    /**
     * Force all the scanner classes to be auto loaded
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $required = array('picon\ClassScanner', 'picon\AnnotationRule', 'picon\ClassNamespaceRule', 'picon\ClassNameRule', 'picon\SubClassRule');
        foreach($required as $class)
        {
            new \ReflectionClass($class);
        }
    }
    
    public function testByAnnotation()
    {
        $scanner = new core\scanner\ClassScanner(new core\scanner\AnnotationRule('picon\core\annotations\Service'));
        $this->performAsserts($scanner, array('TestService', 'TestServiceName'));
    }
    
    public function testByNamespace()
    {
        $scanner = new core\scanner\ClassScanner(new core\scanner\ClassNamespaceRule('testnamespace'));
        $this->performAsserts($scanner, array('testnamespace\TestNameSpaceClassOne', 'testnamespace\TestNameSpaceClassTwo'));
    }
    
    public function testByName()
    {
        $scanner = new ClassScanner(new ClassNameRule('\w*(Scan|Rule){1}\w*'));
        $this->performAsserts($scanner, array('picon\ClassScannerTest', 'picon\ClassScanner', 'picon\AnnotationRule', 'picon\ClassNamespaceRule', 'picon\ClassNameRule', 'picon\SubClassRule'));
    }
    
    public function testBySubClass()
    {
        $scanner = new ClassScanner(new SubClassRule('testnamespace\TestNameSpaceClassOne'));
        $this->performAsserts($scanner, array('testnamespace\TestNameSpaceClassTwo'));
    }
    
    /**
     * @expectedException     InvalidArgumentException
     */
    public function testInvalidRule()
    {
        $scanner = new ClassScanner(new \stdClass());
    }
    
    public function testRuleArray()
    {
        $scanner = new core\scanner\ClassScanner(array(new SubClassRule('testnamespace\TestNameSpaceClassOne'), new core\scanner\ClassNamespaceRule('testnamespace')));
        $this->performAsserts($scanner, array('testnamespace\TestNameSpaceClassOne', 'testnamespace\TestNameSpaceClassTwo'));
    }
    
    /**
     * @expectedException     InvalidArgumentException
     */
    public function testInvalidRuleArray()
    {
        $scanner = new core\scanner\ClassScanner(array(new SubClassRule('testnamespace\TestNameSpaceClassOne'), new \stdClass()));
    }
    
    private function performAsserts(core\scanner\ClassScanner $scanner, $expectedClasses)
    {
        $results = $scanner->scanForName();
        $this->assertEquals($expectedClasses, $results);
        
        $expectedReflections = array();
        foreach($expectedClasses as $expected)
        {
            array_push($expectedReflections, new \ReflectionAnnotatedClass($expected));
        }
        
        $results = $scanner->scanForReflection();
        $this->assertEquals($expectedReflections, $results);
    }
}

?>
