<?php

namespace App\Controller;

use App\Enum\Cache;
use App\Enum\YouTubeVideos;
use App\Service\YouTubeService;
use App\Traits\Locale;
use DateInterval;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AboutController extends AbstractController
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

    /**
     * @var CacheInterface
     */
    private $appCache;

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        YouTubeService $youTubeService,
        CacheInterface $appCache
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->youTubeService = $youTubeService;
        $this->appCache = $appCache;
    }

    /**
     * @Route({"en": "/about", "bg": "/относно"}, name="about", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(Request $request): Response
    {
        if ($this->checkRedirectConditions($request)) {
            return $this->redirectToSecondaryLanguageRoute($request);
        }

        $trailerId = $request->attributes->get('_locale') === 'bg' ?
            YouTubeVideos::TRAILER_BG_ID : YouTubeVideos::TRAILER_ID;

        $trailer = $this->appCache->get($trailerId, function (ItemInterface $cacheItem) use ($trailerId) {
            $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::TWELVE_HOURS_AS_STRING));

            return $this->youTubeService->getSingleVideo($trailerId);
        });

        $response = new Response($this->renderView('about/index.html.twig', [
            'trailer'=> $trailer
        ]));

        $response->headers->set('Content-Language', $request->attributes->get('_locale'));
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setSharedMaxAge(Cache::ONE_HOUR_IN_SECONDS);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}