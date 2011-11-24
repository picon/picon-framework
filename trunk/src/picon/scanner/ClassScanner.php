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

/**
 * Scanner to find classes that match a given set of rules. 
 * This expectes any classes that are to be scanned to have been declared
 * 
 * @author Martin Cassidy
 */
class ClassScanner
{
    private $rules;
    
    /**
     *
     * @param mixed $rule Array of ClassScannerRule or a single ClassScannerRule
     */
    public function __construct($rules = array())
    {
        if(is_array($rules))
        {
            foreach($rules as $rule)
            {
                if(!($rule instanceof ClassNameRule))
                {
                    throw new \InvalidArgumentException(sprintf("Expected an array of ClassScannerRulle. %s is not a ClassScannerRulle"), get_class($rule));
                }
            }
            $this->rules = $rules;
        }
        else
        {
            if(!($rules instanceof ClassScannerRule))
            {
                throw new \InvalidArgumentException(sprintf("Expected ClassScannerRule, actual %s", get_class($rules)));
            }
            $this->rules = array($rules);
        }
    }
    
    /**
     * Add a new rule to the class scanner
     * @param ClassScannerRule $rule The rule to add
     */
    public function addRule(ClassScannerRule $rule)
    {
        array_push($this->rules, $rule);
    }
    
    /**
     * Scan all declared classes for those matching the rules that have been added
     * 
     * @return Array An array of the class names which matched
     */
    public function scan()
    {
        $matchedClasses = array();
        $declaredClasses = get_declared_classes();
        
        foreach($declaredClasses as $className)
        {
            $match = false;
            foreach($this->rules as $rule)
            {
                //@todo Create instance of class
                $match = $rule->matches($object);
                
                if($match)
                {
                    array_push($matchedClasses, $className);
                    break;
                }
            }
        }
        
        return $matchedClasses;
    }
}

?>
