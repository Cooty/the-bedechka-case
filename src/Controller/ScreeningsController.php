<?php

namespace App\Controller;

use App\Repository\ScreeningRepository;
use App\Service\TransportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Enum\Cache;
use App\Traits\Locale;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

class ScreeningsController extends AbstractController
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
     * @var ScreeningRepository
     */
    private $screeningRepository;

    /**
     * @var TransportService
     */
    private $transport;

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        ScreeningRepository $screeningRepository,
        TransportService $transport
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->screeningRepository = $screeningRepository;
        $this->transport = $transport;
    }

    /**
     * @Route({"en": "/watch", "bg": "/гледай"}, name="screenings", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($this->checkRedirectConditions($request)) {
            return $this->redirectToSecondaryLanguageRoute($request);
        }

        $response = new Response($this->renderView('screenings/index.html.twig'));

        $response->headers->set('Content-Language', $request->attributes->get('_locale'));
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setSharedMaxAge(Cache::FULL_PAGE_CACHE_EXPIRATION);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    private function getCurrentAndPastScreenings(string $locale): array
    {
        $current = $this->screeningRepository->findCurrent();
        $currentScreenings = count($current) ?
            $this->transport->makeScreeningsFrontend($current, $locale)
            :
            [];

        $past = $this->screeningRepository->findPast();
        $pastScreenings = count($past) ?
            $this->transport->makeScreeningsFrontend($past, $locale)
            :
            [];

        return [$currentScreenings, $pastScreenings];
    }

    /**
     * Serves the list of past and current screenings as a non-cached ESI fragment
     * @param Request $request
     * @return Response
     */
    public function getScreenings(Request $request): Response
    {
        list($currentScreenings, $pastScreenings) = $this->getCurrentAndPastScreenings($request->getLocale());

        return new Response($this->renderView('screenings/partials/lists.html.twig', [
            'current_screenings' => $currentScreenings,
            'past_screenings' => $pastScreenings
        ]));
    }
}