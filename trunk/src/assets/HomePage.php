<?php

use \picon\MarkupContainer;
use \picon\Link;
use \picon\ListView;
use \picon\ArrayModel;
use \picon\BasicModel;
use \picon\Label;
use \picon\ResourceReference;
use \picon\HeaderResponse;

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
        parent::__construct();
        
        $layoutExamples = array();
        $layoutExamples[] = new Example('Markup Inheretence', MarkupInheritancePage::getIdentifier());
        $layoutExamples[] = new Example('Panels', PanelPage::getIdentifier());
        $layoutExamples[] = new Example('Borders', BorderPage::getIdentifier());
        
        $generalExamples = array();
        $generalExamples[] = new Example('Labels', LabelPage::getIdentifier());
        $generalExamples[] = new Example('Links', LinkPage::getIdentifier());
        $generalExamples[] = new Example('Lists', ListPage::getIdentifier());
        $generalExamples[] = new Example('Tabs', TabPanelPage::getIdentifier());
        
        $formExamples = array();
        $formExamples[] = new Example('Form Fields', FormPage::getIdentifier());
        $formExamples[] = new Example('Validation', ValidationPage::getIdentifier());
        
        $tableExamples = array();
        $tableExamples[] = new Example('Data Table', DataTablePage::getIdentifier());
        
        $ajaxExamples = array();
        $ajaxExamples[] = new Example('Ajax Link', AjaxLinkPage::getIdentifier());
        $ajaxExamples[] = new Example('Ajax Button', AjaxButtonPage::getIdentifier());
        
        $examples = array();
        $examples[] = new ExampleType('General', $generalExamples);
        $examples[] = new ExampleType('Layout', $layoutExamples);
        $examples[] = new ExampleType('Form Components', $formExamples);
        $examples[] = new ExampleType('Data Tables', $tableExamples);
        $examples[] = new ExampleType('Ajax', $ajaxExamples);
        
        $self = $this;
        $this->add(new ListView('examples', function(picon\ListItem $item) use ($self)
        {
            $type = $item->getModelObject();
            $item->add(new picon\Label('title', new picon\BasicModel($type->name)));
            
            $item->add(new ListView('list', function(picon\ListItem $item) use ($self)
            {
                $link = new picon\Link('link', function() use ($item, $self)
                {
                    $self->setPage($item->getModelObject()->page);
                });
                $item->add($link);
                $link->add(new picon\Label('exampleName', new picon\BasicModel($item->getModelObject()->name)));
            }, new picon\ArrayModel($type->examples)));
        }, new ArrayModel($examples)));
        
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/HomePage.php', 'assets/HomePage.html');
    }
}

?>
