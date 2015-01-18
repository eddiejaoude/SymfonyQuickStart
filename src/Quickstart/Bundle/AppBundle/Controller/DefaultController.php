<?php

namespace Quickstart\Bundle\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package Quickstart\Bundle\AppBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('QuickstartAppBundle:Default:index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changelogAction()
    {
        return $this->render(
            'QuickstartAppBundle:Default:changelog.html.twig',
            array(
                'changelog' => file('../changelog')
            )
        );
    }
}
