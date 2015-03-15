<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Context\Context;
use Behat\WebApiExtension;


define('BEHAT_ERROR_REPORTING', E_ERROR | E_WARNING | E_PARSE);

/**
 * Features context
 */
class WebFeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct()
    {
    }

    /**
     * @Given following users for each persona exist on system:
     */
    public function followingUsersForEachPersonaExistOnSystem(TableNode $table)
    {

    }

}

