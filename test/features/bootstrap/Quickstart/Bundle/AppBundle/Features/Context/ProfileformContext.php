<?php

namespace Quickstart\Bundle\AppBundle\Features\Context;

use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Gherkin\Node\TableNode;

class ProfileformContext extends PageObjectContext
{

    /**
     * @param TableNode $table
     * @Given /^I fill form with:$/
     */
    public function iFillFormWith(TableNode $table)
    {

        $this->getPage('Profileform')->fillOutForm($table);
    }

    /**
     * @param TableNode $table
     * @Given /^I should see form with:$/
     */
    public function iShouldSeeFormWith(TableNode $table)
    {
        $this->getPage('Profileform')->checkFormContains($table);
    }

}