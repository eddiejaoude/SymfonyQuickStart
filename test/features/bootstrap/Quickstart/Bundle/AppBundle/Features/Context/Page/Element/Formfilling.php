<?php

namespace Page\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Gherkin\Node\TableNode;

class Formfilling extends Element
{

    /**
     * @var array|string $selector
     */
    protected $selector = '.formtest';


    /**
     * @param TableNode $table
     * @return Page
     * @throws \Exception
     */
    public function fillForm(TableNode $table)
    {
        $page = $this;
        foreach ($table->getRows() as $row) {
            list($fieldSelector, $value) = $row;
            echo 'this is a value of some kind:' . $fieldSelector;
            $field = $page->findField($fieldSelector);
            if (empty($field)) {
                $field = $this->getDriver()->find('//label[contains(normalize-space(string(.)), "' . $fieldSelector . '")]');
                if (!empty($field)) {
                    $field = current($field);
                }
            }
            if (empty($field)) {
                throw new \Exception('Field not found: ' . $fieldSelector);
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
                if ($type == 'checkbox') {
                    if (strtolower($value) == 'yes') {
                        $page->checkField($fieldSelector);
                    } else {
                        $page->uncheckField($fieldSelector);
                    }
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
     * @param TableNode $table
     * @return Page
     * @throws \Exception
     */
    public function assertFormContains(TableNode $table)
    {
        foreach ($table->getRows() as $row) {
            list($field, $value) = $row;

            $node = $this->findField($field);
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
                $optionNodes = $this->getDriver()->find($node->getXpath() . "/option");
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
                    $this->hasCheckedField($option);
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
                throw new \Exception($message);
            }
        }
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument2($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

}