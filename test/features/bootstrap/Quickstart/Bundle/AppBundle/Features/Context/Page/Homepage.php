<?php

namespace Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Mink;

class Homepage extends Page
{

    /**
     * @param string $email
     * @param string $password
     * @return Page
     */
    public function loginCredentials($email, $password)
    {
        $this->clickLink('Login');
        $this->fillField('username', $email);
        $this->fillField('password', $password);
        $this->pressButton('_submit');

    }

    /**
     * @param string $count
     * @param string $area
     * @return Page
     * @throws \Exception
     */
    public function countTheElements($count, $area)
    {
        $str = $this->getDriver()->getContent();
        echo $str;
        $count2 = substr_count($str, $area);
        if ($count != $count2) {
            throw new \Exception('Count is incorrect');
        };
    }



}