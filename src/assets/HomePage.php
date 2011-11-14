<?php

/**
 * Sample Homepage
 *
 * @author Martin
 */
class HomePage extends picon\WebPage
{
    public function __construct()
    {
        $this->add(new picon\MarkupContainer("title"));
        $this->add(new picon\MarkupContainer("stuff"));
    }
}

?>
