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
 * Based on SplEnum, this class allows enumerated types to be created.
 * 
 * Extend this class to create your enums in the following way
 * 
 * <code>
 * class Fruit extends Enum
 * {
 *      const APPLE = 1;
 *      const ORANGE = 2;
 *      const _DEFAULT = 1;
 * }
 * </code>
 * 
 * _DEFAULT is optional and will be used if no paramater is passed to the constructor
 * 
 * <code>
 * echo new Fruit();
 * echo new Fruit(Fruit::ORANGE);
 * echo Fruit::valueOf("orange");
 * </code>
 * 
 * Will output:
 * 1
 * 2
 * 2
 * 
 * @author Martin Cassidy
 * @package domain
 */
abstract class Enum implements Identifiable
{
    private $value;
    const DEFAULT_NAME = "_DEFAULT";
    
    /**
     * Create a new enum
     * @param Object $value The enum value
     */
    public function __construct($value = null)
    {
        $enum = new \ReflectionClass ($this);
        
        if($value==null)
        {
            if(!$enum->hasConstant(self::DEFAULT_NAME))
            {
                throw new \InvalidArgumentException("Cannot create enum as no value has been passed and no default is specified");
            }
            else
            {
                $value = $enum->getConstant(self::DEFAULT_NAME);
            }
        }
        
        foreach($enum->getConstants() as $name => $enumValue)
        { 
            if($enumValue==$value)
            {
                $this->value = $value;
                return;
            }
        }
        
        throw new \InvalidArgumentException("Unknown enum value ".$value);
    }
    
    /**
     * Create an enum for the given value
     * @param String $obj A string of the value
     * @return The enum for the value, or null if no value can be found
     */
    public static function valueOf($obj)
    {
        $enumClass = new \ReflectionClass (get_called_class());
        foreach($enumClass->getConstants() as $enumName => $enumValue)
        {
            if(strtolower($enumValue)==strtolower($obj))
            {
                return new static($obj);
            }
        }
        return null;
    }
    
    /**
     * Gets all the posible values for this enum
     * 
     * @param Boolean include the default value, defaults to false
     * @return Array of values for this enum
     */
    public static function values($includeDefault = false)
    {
        $enumClass = new \ReflectionClass (get_called_class());
        $values = $enumClass->getConstants();
        $processed = array();
        
        foreach($values as $index => $value)
        {
            if($index!="DEFAULT_NAME" && ($includeDefault || $index!=self::DEFAULT_NAME))
            {
                $processed[$index] = $value;
            }
        }
        
        
        return $processed;
    }
    
    /**
     * 
     * @return A String representation of the enum
     */
    public function __toString()
    {
        return "".$this->value;
    }
    
    /**
     * Tests if two enums are equal
     * @param Enum The enum to test against
     * @return Boolean true if the enum's match, false otherwise. False if the passed object is not a valid enum
     */
    public function equals($enum)
    {
        if($enum instanceof Enum)
        {
            return $enum->value==$this->value;
        }
        return false;
    }
    
    public static function getIdentifier()
    {
        return Identifier::forName(get_called_class());
    }

}

?>
