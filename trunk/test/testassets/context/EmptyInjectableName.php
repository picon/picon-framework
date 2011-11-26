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
 * @author Martin Cassidy
 */
class EmptyInjectableName extends AbstractContext
{
    /** @Resource(name = 'testService') */
    protected $testServiceHere;

    /** @Resource(name = 'repo') */
    protected $repoHere;

    /** @Resource(name = 'serv') */
    protected $servHere;
    
    /** @Resource(name = 'testRepository') */
    protected $testRepositoryHere;
    
    public function getTestService()
    {
        return $this->testServiceHere;
    }
    
    public function getTestServ()
    {
        return $this->servHere;
    }
    
    public function getTestRepo()
    {
        return $this->repoHere;
    }
    
    public function getTestRepository()
    {
        return $this->testRepositoryHere;
    }
}

?>
