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
require_once(dirname(__FILE__).'/../../AbstractPiconTest.php');

class ConfigLoaderTest extends AbstractPiconTest
{
    public function testValidConfig()
    {
        $config = ConfigLoader::load(dirname(__FILE__).'/../../config/picon.xml');
        $this->assertSame('HomePage', $config->getHomePage());
        $this->assertSame('auto', $config->getStartUp());
        
        $config = ConfigLoader::load(dirname(__FILE__).'/../../config/full.xml');
        $this->assertSame('HomePage', $config->getHomePage());
        $this->assertSame('auto', $config->getStartUp());
        $this->assertTrue($config->getProfile()->isCacheMarkup());
        $this->assertFalse($config->getProfile()->isShowPiconTags());
        $this->assertFalse($config->getProfile()->isCleanBeforeOutput());
        
        
        $sources = $config->getDataSources();
        $this->assertCount(1, $sources);
        
        $source = $sources[0];
        $this->assertSame($source->name, 'testSource');
        $this->assertEquals($source->type, DataSourceType::valueOf("MySQL"));
        $this->assertSame($source->port, '3306');
        $this->assertSame($source->host, 'localhost');
        $this->assertSame($source->username, 'someuser');
        $this->assertSame($source->password, 'somepassword');
        $this->assertSame($source->database, 'somedb');
    }
    
    /**
     * @expectedException \picon\ConfigException
     */
    public function testSchemaValidation()
    {
        ConfigLoader::load(dirname(__FILE__).'/../../resources/badconfig.xml');
    }
    
    /**
     * @expectedException \picon\ConfigException
     */
    public function testMissingProfile()
    {
        ConfigLoader::load(dirname(__FILE__).'/../../resources/missingprofile.xml');
    }
}

?>
