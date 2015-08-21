<?php

use picon\web\ListView;
use picon\web\ArrayModel;
use picon\web\Label;
use picon\web\ListItem;
use picon\web\BasicModel;
use picon\web\Link;

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
        $formExamples[] = new Example('Special Fields', SpecialFields::getIdentifier());
        
        $tableExamples = array();
        $tableExamples[] = new Example('Data Table', DataTablePage::getIdentifier());
        
        $ajaxExamples = array();
        $ajaxExamples[] = new Example('Ajax Link', AjaxLinkPage::getIdentifier());
        $ajaxExamples[] = new Example('Ajax Button', AjaxButtonPage::getIdentifier());
        
        $authoExamples [] = new Example('Authorised Access Page', AuthorisedPage::getIdentifier());
        
        $examples = array();
        $examples[] = new ExampleType('General', $generalExamples);
        $examples[] = new ExampleType('Layout', $layoutExamples);
        $examples[] = new ExampleType('Form Components', $formExamples);
        $examples[] = new ExampleType('Data Tables', $tableExamples);
        $examples[] = new ExampleType('Ajax', $ajaxExamples);
        $examples[] = new ExampleType('Security', $authoExamples);
        
        $self = $this;
        $this->add(new ListView('examples', function(ListItem $item) use ($self)
        {
            $type = $item->getModelObject();
            $item->add(new Label('title', new BasicModel($type->name)));
            $examples = $type->examples;
            $item->add(new ListView('list', function(ListItem $item) use ($self, $examples)
            {
                $link = new Link('link', function() use ($item, $self)
                {
                    $self->setPage($item->getModelObject()->page);
                });
                $item->add($link);
                $link->add(new Label('exampleName', new BasicModel($item->getModelObject()->name)));
            }, new ArrayModel($examples)));
        }, new ArrayModel($examples)));
        
    }
    
    public function getInvolvedFiles()
    {
        return array('assets/HomePage.php', 'assets/HomePage.html');
    }
}

?>
