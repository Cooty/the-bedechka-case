<?php

namespace App\Controller;

use App\Traits\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    use Locale;

    /**
     * @var Twig_Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $secondaryLocale;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(Twig_Environment $twig, string $secondaryLocale, SessionInterface $session)
    {
        $this->twig = $twig;
        $this->secondaryLocale = $secondaryLocale;
        $this->session = $session;
    }

    /**
     * @Route({"en": "/", "bg": "/начало"}, name="home")
     * @param Request $request
     * @param UrlGeneratorInterface $router
     * @return Response
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function index(Request $request, UrlGeneratorInterface $router): Response
    {

//        if($request->getLocale() !== $this->secondaryLocale && $this->detectSupportForSecondaryLocale($request)) {
//            return $this->redirectToRoute('home', ['_locale'=>$this->secondaryLocale]);
//        }
        if($this->detectSupportForLocale($request, $this->secondaryLocale)) {

        }

        $html = $this->twig->render('home/index.html.twig');

        return new Response($html);
    }
}