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
 * Test for the class scanner
 * @todo test multiple rules, test scanning subsets
 * @author Martin Cassidy
 */
class ScannerTest extends AbstractPiconTest
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
            new ReflectionClass($class);
        }
    }
    
    public function testByAnnotation()
    {
        $scanner = new \picon\ClassScanner(new \picon\AnnotationRule('Service'));
        $this->performAsserts($scanner, array('TestService', 'TestServiceName'));
    }
    
    public function testByNamespace()
    {
        $scanner = new \picon\ClassScanner(new \picon\ClassNamespaceRule('testnamespace'));
        $this->performAsserts($scanner, array('testnamespace\TestNameSpaceClassOne', 'testnamespace\TestNameSpaceClassTwo'));
    }
    
    public function testByName()
    {
        $scanner = new \picon\ClassScanner(new \picon\ClassNameRule('\w*(Scan|Rule){1}\w*'));
        $this->performAsserts($scanner, array('ScannerTest', 'picon\ClassScanner', 'picon\AnnotationRule', 'picon\ClassNamespaceRule', 'picon\ClassNameRule', 'picon\SubClassRule'));
    }
    
    public function testBySubClass()
    {
        $scanner = new \picon\ClassScanner(new \picon\SubClassRule('testnamespace\TestNameSpaceClassOne'));
        $this->performAsserts($scanner, array('testnamespace\TestNameSpaceClassTwo'));
    }
    
    private function performAsserts(\picon\ClassScanner $scanner, $expectedClasses)
    {
        $results = $scanner->scanForName();
        $this->assertEquals($expectedClasses, $results);
        
        $expectedReflections = array();
        foreach($expectedClasses as $expected)
        {
            array_push($expectedReflections, new ReflectionAnnotatedClass($expected));
        }
        
        $results = $scanner->scanForReflection();
        $this->assertEquals($expectedReflections, $results);
    }
}

?>
