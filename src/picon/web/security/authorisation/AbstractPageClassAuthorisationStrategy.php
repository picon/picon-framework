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

namespace picon\web\security\authorisation;

use picon\core\Args;
use picon\core\domain\Identifier;
use picon\core\PiconApplication;
use picon\web\Component;
use picon\web\pages\WebPage;

/**
 * An authorisation strategy for web pages. A page requires an authorised
 * use if it is a sub class of a given page
 * 
 * @author Martin Cassidy
 * @package web/security/authorisation
 */
abstract class AbstractPageClassAuthorisationStrategy implements AuthorisationStrategy
{
    private $pageAuthIdentifier;
    private $loginPageIdentifier;
    
    public function __construct(Identifier $pageIdentifier, Identifier $loginPage)
    {
        Args::identifierOf($pageIdentifier, WebPage::getIdentifier(), 'pageIdentifier');
        Args::identifierOf($loginPage, WebPage::getIdentifier(), 'loginPage');
        $this->pageAuthIdentifier = $pageIdentifier;
        $this->loginPageIdentifier = $loginPage;
        PiconApplication::get()->getSecuritySettings()->setComponentNotAuthorisedListener(new DirectToPageComponentNotAuthorisedListener($loginPage));
    }
    
    public function isComponentInstantiationAuthorised(Component $component)
    {
        if($component instanceof WebPage && $component->getIdentifier()->of($this->pageAuthIdentifier))
        {
            return $this->isAuthorised();
        }
        return true;
    }
    
    protected abstract function isAuthorised();
}

?>
