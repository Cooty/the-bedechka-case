<?php

namespace App\Controller;

use App\Service\YouTubeService;
use App\Enum\YouTubeVideos;
use App\Traits\Locale;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use App\Enum\Cache;

class PeopleController extends AbstractController
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
     * @var YouTubeService
     */
    private $youTubeService;

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        YouTubeService $youTubeService
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->youTubeService = $youTubeService;
    }

    /**
     * @Route({"en": "/protagonists", "bg": "/участници"}, name="people", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($this->checkRedirectConditions($request)) {
            return $this->redirectToSecondaryLanguageRoute($request);
        }

        $videos = $this->youTubeService->getVideosFromPlaylist(YouTubeVideos::PROTAGONISTS_PLAYLIST_ID);

        $response = new Response($this->renderView('people/index.html.twig', [
            'videos' => $videos
        ]));
        $response->headers->set('Content-Language', $request->attributes->get('_locale'));
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setSharedMaxAge(Cache::FULL_PAGE_CACHE_EXPIRATION);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}