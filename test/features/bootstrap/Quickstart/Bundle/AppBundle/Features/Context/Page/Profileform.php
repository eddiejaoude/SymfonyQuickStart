<?php

namespace Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Gherkin\Node\TableNode;

class Profileform extends Page
{

    /**
     * @param TableNode $table
     * @return Page
     * @throws \Exception
     */
    public function fillOutForm(TableNode $table)
    {
        return $this->getElement('Formfilling')->fillForm($table);
    }

    /**
     * @param TableNode $table
     * @return Page
     * @throws \Exception
     */
    public function checkFormContains(TableNode $table)
    {
        return $this->getElement('Formfilling')->assertFormContains($table);
    }

}