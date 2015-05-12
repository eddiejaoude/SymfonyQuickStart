<?php

namespace Quickstart\Bundle\AppBundle\Features\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;

/**
 * Features context
 */
class FeatureContext implements Context, SnippetAcceptingContext
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

