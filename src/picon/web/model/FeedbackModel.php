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
 * Description of FeedbackModel
 * 
 * @author Martin Cassidy
 */
class FeedbackModel extends ArrayModel
{
    public function __construct()
    {
        parent::__construct(array());
    }
    
    /**
     * @todo sort of referencing here to avoid getting then setting
     * @param FeedbackMessage $message 
     */
    public function addMessage(FeedbackMessage $message)
    {
        $messages = $this->getModelObject();
        array_push($messages, $message);
        $this->setModelObject($messages);
    }
    
    /**
     * Cleanup old messages
     */
    public function __wakeup()
    {
        $array = array();
        $this->setModelObject($array);
    }
    
    public function setModelObject(&$object)
    {
        parent::setModelObject($object);
    }
    
    public function hasMessages(Component $reporter, $level = null)
    {
        foreach($this->getModelObject() as $message)
        {
            if($message->reporter==$reporter && ($level==null || $message->level==$level))
            {
                return true;
            }
        }
        return false;
    }
}

?>
