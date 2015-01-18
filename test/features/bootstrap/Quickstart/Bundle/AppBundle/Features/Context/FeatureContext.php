<?php

namespace Quickstart\Bundle\AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Class FeatureContext
 * @package Quickstart\Bundle\AppBundle\Features\Context
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext

{
    private $_parameters = array();

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     * @param   array $parameters Context parameters (set them up through behat.yml)
     */

    public function __construct()
    {

    }

    public function getParameter($name)
    {
        if (count($this->_parameters) === 0) {
            throw new \Exception('Parameters not loaded!');
        } else {
            $parameters = $this->_parameters;
            return (isset($parameters[$name])) ? $parameters[$name] : null;
        }
    }

    public function spin($lambda, $wait = 60)
    {
        for ($i = 0; $i < $wait; $i++) {
            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (\Exception $e) {
                // do nothing
            }
            sleep(1);
        }

        $backtrace = debug_backtrace();

        throw new \Exception(
            "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n" .
            $backtrace[1]['file'] . ", line " . $backtrace[1]['line']
        );
    }

    /**
     * @Given /^I fill form with:$/
     */
    public function fillForm(TableNode $table)
    {
        $page = $this->getSession()->getPage();

        foreach ($table->getRows() as $row) {
            list($fieldSelector, $value) = $row;

            $field = $page->findField($fieldSelector);
            if (empty($field)) {
                $field = $this->getSession()->getDriver()->find('//label[contains(normalize-space(string(.)), "' . $fieldSelector . '")]');
                if (!empty($field)) {
                    $field = current($field);
                }
            }

            if (empty($field)) {
                die('Field not found ' . $fieldSelector. PHP_EOL);
            }

            $tag = strtolower($field->getTagName());

            if ($tag == 'textarea') {
                $page->fillField($fieldSelector, $value);
            } elseif ($tag == 'select') {
                if ($field->hasAttribute('multiple')) {
                    foreach (explode(',', $value) as $index => $option) {
                        $page->selectFieldOption($fieldSelector, trim($option), true);
                    }
                } else {
                    $page->selectFieldOption($fieldSelector, $value);
                }
            } elseif ($tag == 'input') {
                $type = strtolower($field->getAttribute('type'));
                if ($type == 'checkbox' || $type == 'radio') {
                    if (strtolower($value) == 'yes') {
                        $page->checkField($fieldSelector);
                    } else {
                        $page->uncheckField($fieldSelector);
                    }
//                } elseif ($type == 'radio') {
//                    // TODO: handle radio
                } else {
                    $page->fillField($fieldSelector, $value);
                }
            } elseif ($tag == 'label') {
                foreach (explode(',', $value) as $option) {
                    $option = $this->fixStepArgument(trim($option));
                    $field->getParent()->checkField($option);
                }
            }
        }
    }

    /**
     * @Given /^I should see form with:$/
     */
    public function assertFormContain(TableNode $table)
    {
        foreach ($table->getRows() as $row) {
            list($field, $value) = $row;

            $node = $this->getSession()->getPage()->findField($field);
            if (empty($node)) {
                $node = $this->getSession()->getDriver()->find('//label[contains(normalize-space(string(.)), "' . $field . '")]');
                if (!empty($node)) {
                    $node = current($node);
                }
            }

            if (null === $node) {
                throw new \Exception($this->getSession(), 'form field', 'id|name|label|value', $field);
            }

            if ($node->getTagName() == 'input' && in_array($node->getAttribute('type'), array('checkbox', 'radio'))) {
                $actual = $node->isChecked() ? 'YES' : 'NO';
            } elseif ($node->getTagName() == 'select') {
                $actual = $node->getValue();
                if (!is_array($actual)) {
                    $actual = array($actual);
                }

                $options = array();
                $optionNodes = $this->getSession()->getDriver()->find($node->getXpath() . "/option");
                foreach ($optionNodes as $optionNode) {
                    $options[$optionNode->getValue()] = $optionNode->getText();
                    $options[$optionNode->getText()] = $optionNode->getText();
                }
                foreach ($actual as $index => $optionValue) {
                    if (isset($options[$optionValue])) {
                        $actual[$index] = $options[$optionValue];
                    }
                }
            } elseif ($node->getTagName() == 'label') {
                foreach (explode(',', $value) as $option) {
                    $option = $this->fixStepArgument(trim($option));
                    $this->assertSession()->checkboxChecked($option);
                }
                return true;
            } else {
                $actual = $node->getValue();
            }

            if (is_array($actual)) {
                $actual = join(',', $actual);
            }
            $regex = '/^' . preg_quote($value, '$/') . '/ui';

            if (!preg_match($regex, $actual)) {
                $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $field, $actual, $value);
                throw new \Exception($message, $this->getSession());
            }
        }
    }

    /**
     * @Given /^I fill in "(?P<field>(?:[^"]|\\")*)" with:$/
     */
    public function iFillInWith($field, PyStringNode $string)
    {
        $field = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($string->getRaw());
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * @Given /^the "(?P<field>[^"]*)" field should contain:$/
     */
    public function assertFieldShouldContain($field, PyStringNode $string)
    {
        $this->assertSession()->fieldValueEquals($field, $string->getRaw());
    }

    /**
     * Checks, that form field with specified id|name|label|value has specified value.
     *
     * @Then /^the "(?P<field>(?:[^"]|\\")*)" multiple field should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertFieldContains($field, $value)
    {
        $node = $this->assertSession()->fieldExists($field);
        $actual = $node->getValue();
        if (is_array($actual)) {
            $actual = join(',', $actual);
        }
        $regex = '/^' . preg_quote($value, '$/') . '/ui';

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The field "%s" value is "%s", but "%s" expected.', $field, $actual, $value);
            throw new \Exception($message, $this->getSession());
        }
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }


    /**
     * @When /^I check the "([^"]*)" radio button$/
     */
    public function iCheckTheRadioButton($radioLabel)
    {
        $radioButton = $this->getSession()->getPage()->findField($radioLabel);
        if (null === $radioButton) {
            throw new \Exception("Cannot find radio button " . $radioLabel);
        }
        $value = $radioButton->getAttribute('value');
        $this->getSession()->getDriver()->click($radioButton->getXPath());
        sleep(1);
    }

    /**
     * @Then /^count of "([^"]*)" instances of "(?P<text>[^"]*)" exists on page$/
     */
    public function thePersonsAreUnlinked($count, $area)
    {
        $str = $this->getSession()->getPage()->getContent();
        $count2 = substr_count($str, $area);
        if ($count === $count2) {
            echo 'The count of:' . $count2 . ' is correct';
        } else {
            throw new \Exception("Count " . $count . " expected but got " . $count2);
        }
    }

    /**
     * @Then /^the server code response should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($httpStatus)
    {
        if ((string)$this->_response->getStatusCode() !== $httpStatus) {
            throw new \Exception('HTTP code does not match ' . $httpStatus .
                ' (actual: ' . $this->_response->getStatusCode() . ')');
        }
    }

    /**
     * @Given I am logged in as :arg1 with password :arg2
     */
    public function iAmLoggedInAsWithPassword($email, $password)
    {
        $this->visitPath('/');
        $this->getSession()->getPage()->clickLink('Login');
        $this->getSession()->getPage()->fillField('username', $email);
        $this->getSession()->getPage()->fillField('password', $password);
        $this->getSession()->getPage()->pressButton('_submit');
    }

    /**
     * @Given following users for each persona exist on system:
     */
    public function followingUsersForEachPersonaExistOnSystem(TableNode $table)
    {

    }
}

