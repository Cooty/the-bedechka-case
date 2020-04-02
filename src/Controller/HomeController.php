<?php

namespace App\Controller;

use App\Service\NewsService;
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

    public function __construct(
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey,
        NewsService $newsService,
        CacheInterface $appCache,
        LoggerInterface $logger
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->newsService = $newsService;
        $this->appCache = $appCache;
        $this->logger = $logger;
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

        $response = new Response($this->renderView('home/index.html.twig'));

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