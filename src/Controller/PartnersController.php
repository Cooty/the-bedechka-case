<?php

namespace App\Controller;

use App\Service\StaticDataService;
use App\Traits\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use App\Enum\Cache;

class PartnersController extends AbstractController
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
     * @var StaticDataService
     */
    private $staticData;

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        StaticDataService $staticData
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->staticData = $staticData;
    }

    /**
     * @Route({"en": "/partners", "bg": "/партньори"}, name="partners", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($this->checkRedirectConditions($request)) {
            return $this->redirectToSecondaryLanguageRoute($request);
        }

        $response = new Response($this->renderView('partners/index.html.twig', [
            'partners' => $this->staticData->getDataFromFile('partners.json')
        ]));

        $response->headers->set('Content-Language', $request->attributes->get('_locale'));
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setSharedMaxAge(Cache::FULL_PAGE_CACHE_EXPIRATION);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}