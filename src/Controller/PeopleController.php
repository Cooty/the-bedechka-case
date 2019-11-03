<?php

namespace App\Controller;

use App\Traits\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class PeopleController extends AbstractController
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
     * @var string
     */
    private $languageSettingParamName;
    /**
     * @var string
     */
    private $languageSettingSessionKey;

    public function __construct(
        Twig_Environment $twig,
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey
    ) {
        $this->twig = $twig;
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
    }

    /**
     * @Route({"en": "/people", "bg": "/участници"}, name="people")
     * @param Request $request
     * @return Response
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function index(Request $request): Response
    {
        if($this->checkRedirectConditions($request)) {
            return $this->redirectToSecondaryLanguageRoute($request);
        }

        $html = $this->twig->render('people/index.html.twig');

        $response = new Response($html);
        $response->headers->set('Content-Language', $request->attributes->get('_locale'));

        return $response;
    }
}