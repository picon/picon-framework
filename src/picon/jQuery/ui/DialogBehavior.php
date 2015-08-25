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

namespace picon\jquery\ui;

use picon\core\Args;
use picon\jquery\BooleanOption;
use picon\jquery\CallbackFunctionOption;
use picon\jquery\NumbericOption;

/**
 * Description of DialogBehavior
 * 
 * @author Martin Cassidy
 */
class DialogBehavior extends DefaultJQueryUIBehaviour
{
    public function __construct()
    {
        parent::__construct('dialog');
    }
    
    public function setModal($modal)
    {
        Args::isBoolean($modal, 'modal');
        $this->getOptions()->add(new BooleanOption('modal', $modal));
    }
    
    public function setAutoOpen($autoOpen)
    {
        Args::isBoolean($autoOpen, 'autoOpen');
        $this->getOptions()->add(new BooleanOption('autoOpen', $autoOpen));
    }
    
    public function setHeight($height)
    {
        Args::isNumeric($height, 'height');
        $this->getOptions()->add(new NumbericOption('height', $height));
    }
    
    public function setWidth($width)
    {
        Args::isNumeric($width, 'width');
        $this->getOptions()->add(new NumbericOption('width', $width));
    }
    
    public function setBeforeClose($beforeClose, $function = '')
    {
        Args::callBackArgs($beforeClose, 1, 'beforeClose');
        $this->getOptions()->add(new CallbackFunctionOption('beforeClose', $beforeClose, $function));
    }
}

?>
