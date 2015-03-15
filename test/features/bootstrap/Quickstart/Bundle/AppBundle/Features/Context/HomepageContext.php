<?php

use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class HomepageContext extends PageObjectContext
{

    public function __construct()
    {
    }

    /**
     * @Given I am logged in as :arg1 with password :arg2
     */
    public function iAmLoggedInAsWithPassword($email, $password)
    {
        $this->getPage('Homepage')->loginCredentials($email, $password);
    }

    /**
     * @Then /^count of "([^"]*)" instances of "(?P<text>[^"]*)" exists on page$/
     */
    public function countOfExistsOnPage($count, $area)
    {
        $this->getPage('Homepage')->countTheElements($count, $area);
    }
}