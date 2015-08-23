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

namespace picon\web\domain;

use picon\core\domain\CommonDomainBase;
use picon\web\Component;

/**
 * A feedback message for use by the feedback message model and feedback panel
 * 
 * @author Martin Cassidy
 * @package web/domain
 */
class FeedbackMessage extends CommonDomainBase
{
    const FEEDBACK_MESSAGE_FATAL = '1';
    const FEEDBACK_MESSAGE_ERROR = '2';
    const FEEDBACK_MESSAGE_WARNING = '3';
    const FEEDBACK_MESSAGE_INFO = '4';
    const FEEDBACK_MESSAGE_SUCCESS = '5';
    
    private $level;
    private $message;
    private $reporter;
    
    public function __construct($level, $message, Component &$reporter)
    {
        $this->level = $level;
        $this->message = $message;
        $this->reporter = $reporter;
    }
}

?>
