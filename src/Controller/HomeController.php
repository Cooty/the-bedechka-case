<?php

namespace App\Controller;

use App\Service\NewsService;
use App\Service\YouTubeService;
use App\Traits\Locale;
use DateInterval;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use App\Enum\Cache;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Enum\YouTubeVideos;

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

    /**
     * @var CacheInterface
     */
    private $appCache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var YouTubeService
     */
    private $youTubeService;

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        NewsService $newsService,
        CacheInterface $appCache,
        LoggerInterface $logger,
        YouTubeService $youTubeService
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->newsService = $newsService;
        $this->appCache = $appCache;
        $this->logger = $logger;
        $this->youTubeService = $youTubeService;
    }

    private function getVideos(): array
    {
        $theVisionSectionVideosSizeMap = ['mobile'=> 'standard', 'tablet'=> 'high', 'desktop'=> 'standard'];

        try {
            $trailer = $this->appCache->get(YouTubeVideos::TRAILER_ID, function (ItemInterface $cacheItem) {
                $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::VIDEO_EXPIRATION));

                return $this->youTubeService->getSingleVideo(YouTubeVideos::TRAILER_ID);
            });

            $thf = $this->appCache->get(
                YouTubeVideos::THF_ID,
                function (ItemInterface $cacheItem) use ($theVisionSectionVideosSizeMap) {
                    $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::VIDEO_EXPIRATION));

                    return $this->
                            youTubeService->
                            getSingleVideo(YouTubeVideos::THF_ID, $theVisionSectionVideosSizeMap);
            });

            $lifeInTheJungle = $this->appCache->get(
                YouTubeVideos::LIFE_IN_THE_JUNGLE_ID,
                function (ItemInterface $cacheItem) use ($theVisionSectionVideosSizeMap) {
                    $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::VIDEO_EXPIRATION));

                    return $this->
                            youTubeService->
                            getSingleVideo(YouTubeVideos::LIFE_IN_THE_JUNGLE_ID, $theVisionSectionVideosSizeMap);
            });

            return [$trailer, $thf, $lifeInTheJungle];
        } catch (InvalidArgumentException $e) {
            return [];
        }
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

        list($trailer, $thf, $lifeInTheJungle) = $this->getVideos();

        $response = new Response($this->renderView('home/index.html.twig', [
            'trailer'=> $trailer,
            'thf'=> $thf,
            'lifeInTheJungle'=> $lifeInTheJungle
        ]));

        $response->headers->set('Content-Language', $request->attributes->get('_locale'));
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setSharedMaxAge(Cache::FULL_PAGE_CACHE_EXPIRATION);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    /**
     * Serves the News section as a non-cached ESI fragment
     * @return Response
     */
    public function getNewsSection(): Response
    {
        try {
            $data = $this->appCache->get('news-section', function (ItemInterface $cacheItem) {
                $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::API_RESPONSE_EXPIRATION));

                return [
                    'news_first_page' => $this->newsService->getFirstPage(),
                    'news_has_pagination' => $this->newsService->hasPagination()
                ];
            });
        } catch (InvalidArgumentException $e) {

            $data = [];
        }

        $response = new Response($this->renderView('home/partials/in-the-press.html.twig', $data));

        return $response;
    }

    /**
     * Serves the js config object as a non-cached ESI fragment
     * @return Response
     */
    public function jsConfig(): Response
    {
        $response = new Response($this->renderView('home/partials/js-config.html.twig', [
            'news_has_pagination' => $this->newsService->hasPagination()
        ]));

        return $response;
    }
}