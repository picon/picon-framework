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


namespace picon\test\core\scanner;

use picon\core\scanner\AnnotationRule;
use picon\core\scanner\ClassNameRule;
use picon\core\scanner\ClassNamespaceRule;
use picon\core\scanner\ClassScanner;
use picon\core\scanner\SubClassRule;
use picon\test\core\AbstractPiconUnitTest;

/**
 * Test for the class scanner
 * @todo test multiple rules, test scanning subsets
 * @author Martin Cassidy
 */
class ClassScannerTest extends AbstractPiconUnitTest
{
    /**
     * Force all the scanner classes to be auto loaded
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $required = array('picon\core\scanner\ClassScanner', 'picon\core\scanner\AnnotationRule', 'picon\core\scanner\ClassNamespaceRule', 'picon\core\scanner\ClassNameRule', 'picon\core\scanner\SubClassRule');
        foreach($required as $class)
        {
            new \ReflectionClass($class);
        }
    }
    
    public function testByAnnotation()
    {
        $scanner = new ClassScanner(new AnnotationRule('picon\core\annotations\Service'));
        $this->performAsserts($scanner, array('picon\test\app\TestService', 'picon\test\app\TestServiceName'));
    }
    
    public function testByNamespace()
    {
        $scanner = new ClassScanner(new ClassNamespaceRule('picon\test\app\scanner'));
        $this->performAsserts($scanner, array('picon\test\app\scanner\TestNameSpaceClassOne', 'picon\test\app\scanner\TestNameSpaceClassTwo'));
    }
    
    public function testByName()
    {
        $scanner = new ClassScanner(new ClassNamespaceRule('picon\core\scanner'), new ClassNameRule('\w*(Scan|Rule){1}\w*'));
        $this->performAsserts($scanner, array('picon\core\scanner\ClassScanner', 'picon\core\scanner\AnnotationRule', 'picon\core\scanner\ClassNamespaceRule', 'picon\core\scanner\ClassNameRule', 'picon\core\scanner\SubClassRule'));
    }
    
    public function testBySubClass()
    {
        $scanner = new ClassScanner(array(new SubClassRule('picon\test\app\scanner\TestNameSpaceClassOne')));
        $this->performAsserts($scanner, array('picon\test\app\scanner\TestNameSpaceClassTwo'));
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
        $scanner = new ClassScanner(array(new SubClassRule('picon\test\app\scanner\TestNameSpaceClassOne'), new ClassNamespaceRule('picon\test\app\scanner')));
        $this->performAsserts($scanner, array('picon\test\app\scanner\TestNameSpaceClassOne', 'picon\test\app\scanner\TestNameSpaceClassTwo'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRuleArray()
    {
        $scanner = new ClassScanner(array(new SubClassRule('picon\test\app\scanner\TestNameSpaceClassOne'), new \stdClass()));
    }
    
    private function performAsserts(ClassScanner $scanner, $expectedClasses)
    {
        $results = $scanner->scanForName();
        $this->assertEquals($expectedClasses, $results);
        
        $expectedReflections = array();
        foreach($expectedClasses as $expected)
        {
            array_push($expectedReflections, new \ReflectionClass($expected));
        }
        
        $results = $scanner->scanForReflection();
        $this->assertEquals($expectedReflections, $results);
    }
}

?>
