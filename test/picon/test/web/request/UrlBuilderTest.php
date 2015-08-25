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

namespace picon\test\web\request;

use picon\web\request\UrlBuilder;


/**
 * Description of UrlBuilderTest
 *
 * @author Martin
 */
class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $builder = UrlBuilder::url("example.com");
        $this->assertSame("http://example.com", $builder->build());
    }
    
    public function testPath()
    {
        $builder = UrlBuilder::url("example.com");
        $builder->path("some/path");
        $this->assertSame("http://example.com/some/path", $builder->build());
        
        $builderLeading = UrlBuilder::url("example.com");
        $builderLeading->path("/some/path");
        $this->assertSame("http://example.com/some/path", $builderLeading->build());
    }
    
    public function testParameters()
    {
        $builder = UrlBuilder::url("example.com");
        $builder->parameter("var1", "value1");
        $builder->parameter("var2", "value2");
        $this->assertSame("http://example.com?var1=value1&var2=value2", $builder->build());
    }
    
    public function testProtocol()
    {
        $builder = UrlBuilder::url("example.com");
        $builder->protocol("https");
        $this->assertSame("https://example.com", $builder->build());
    }
    
    public function testAll()
    {
        $builder = UrlBuilder::url("example.com");
        $builder->protocol("https");
        $builder->path("some/path");
        $builder->parameter("var1", "value1");
        $builder->parameter("var2", "value2");
        $this->assertSame("https://example.com/some/path?var1=value1&var2=value2", $builder->build());
    }
}

?>
