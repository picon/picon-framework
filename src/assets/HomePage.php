<?php

/**
 * Sample Homepage
 *
 * @author Martin Cassidy
 * @Path(path = 'home')
 */
class HomePage extends AbstractPage
{
    public function __construct()
    {
        $one = new picon\MarkupContainer("one");
        $two = new picon\MarkupContainer("two");
        $one->add($two);
        $this->add($one);
        $me = $this;
        $two->add(new picon\Link('link', function() use($me)
        {
            $me->setPage(Page2::getIdentifier());
        }));
        
        $this->add(new ExamplePanel('samplePanel'));
    }
}

?>
