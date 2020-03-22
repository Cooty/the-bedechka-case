<?php

namespace App\Controller;

use App\Service\NewsService;
use App\Traits\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    use Locale;

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
     * @var NewsService
     */
    private $newsService;

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        NewsService $newsService
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->newsService = $newsService;
    }

    /**
     * @Route({"en": "/", "bg": "/начало"}, name="home", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($this->checkRedirectConditions($request)) {
            return $this->redirectToSecondaryLanguageRoute($request);
        }

        $response = new Response($this->renderView('home/index.html.twig', [
            'news_first_page' => $this->newsService->getFirstPage(),
            'news_has_pagination' => $this->newsService->hasPagination()
        ]));
        $response->headers->set('Content-Language', $request->attributes->get('_locale'));

        return $response;
    }
}