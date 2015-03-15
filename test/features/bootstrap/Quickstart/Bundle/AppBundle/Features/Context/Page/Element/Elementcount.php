<?php

namespace Page\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\MinkExtension\Context;

class Elementcount extends Element
{

    /**
     * @param string $count
     * @param string $area
     * @param string $str
     * @return Page
     * @throws \Exception
     */
    public function countOfElements($str, $count, $area)
    {
        $count2 = substr_count($str, $area);
        if ($count != $count2) {
            throw new \Exception('Count is incorrect');
        };
    }
}