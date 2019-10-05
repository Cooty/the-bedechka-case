<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class HomeController extends AbstractController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/{_locale}", name="home", locale="en", requirements={"_locale": "en|bg"})
     * @return Response
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws Twig_Error_Loader
     */
    public function home(): Response
    {
        $html = $this->twig->render('home/index.html.twig');

        return new Response($html);
    }
}