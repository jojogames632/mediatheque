<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrivacyController extends AbstractController
{
    /**
     * @Route("/politique-de-confidentialite", name="privacy_politic")
     */
    public function showPrivacyPolitic(): Response
    {
        return $this->render('privacy/index.html.twig');
    }

    /**
     * @Route("/cookies", name="cookies_politic")
     */
    public function showCookiesPolitic(): Response
    {
        return $this->render('privacy/cookies.html.twig');
    }
}
