<?php

namespace App\Controller;

use App\Service\YouTubeService;
use App\Enum\YouTubeVideos;
use App\Traits\Locale;
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
use DateInterval;

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

    /**
     * @var CacheInterface
     */
    private $appCache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        YouTubeService $youTubeService,
        CacheInterface $appCache,
        LoggerInterface $logger
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->youTubeService = $youTubeService;
        $this->appCache = $appCache;
        $this->logger = $logger;
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

        $videos = [];

        try {
            $videos = $this->appCache->get('protagonist_videos', function(ItemInterface $cacheItem) {
                $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::FIVE_HOURS_AS_STRING));
                return $this->youTubeService->getVideosFromPlaylist(YouTubeVideos::PROTAGONISTS_PLAYLIST_ID);
            });
        } catch(\Exception $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
        }catch (InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
        }

        $response = new Response($this->renderView('people/index.html.twig', [
            'videos' => $videos
        ]));

        $response->headers->set('Content-Language', $request->attributes->get('_locale'));
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setSharedMaxAge(Cache::ONE_HOUR_IN_SECONDS);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}