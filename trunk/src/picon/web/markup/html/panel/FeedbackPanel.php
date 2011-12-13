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
 * Panel to display feedback messages
 * 
 * @todo add message level as a css class attribute
 * @todo add support for feedback message filtering
 * @author Martin Cassidy
 */
class FeedbackPanel extends Panel
{
    private $messages;
    
    public function __construct($id)
    {
        parent::__construct($id);
        //@todo update to no fq names when serializer update is done
        $this->messages = new ListView('messages', function($item)
        {
            $item->add(new \picon\Label('message', new \picon\BasicModel($item->getModelObject()->message)));
        }, self::getFeedbackModel());
        
        
        $this->add($this->messages);
    }
    
    public function beforeRender()
    {
        $this->messages->setModel($this->getFeedbackModel());
        parent::beforeRender();
    }
}

?>
