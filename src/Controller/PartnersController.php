<?php

namespace App\Controller;

use App\Traits\Locale;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig_Environment;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use App\Service\StaticDataService;

class PartnersController extends AbstractController
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
    /**
     * @var StaticDataService
     */
    private $staticData;

    public function __construct(
        Twig_Environment $twig,
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        StaticDataService $staticData
    ) {
        $this->twig = $twig;
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->staticData = $staticData;
    }

    /**
     * @Route({"en": "/partners", "bg": "/партньори"}, name="partners")
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


        $data = $this->staticData->getDataFromFile('partners.json');
        $html = $this->twig->render('partners/index.html.twig', [
            'partners'=> $data
        ]);

        $response = new Response($html);
        $response->headers->set('Content-Language', $request->attributes->get('_locale'));

        return $response;
    }
}