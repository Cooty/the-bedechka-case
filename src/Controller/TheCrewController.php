<?php

namespace App\Controller;

use App\Repository\CrewMemberRepository;
use App\Service\TransportService;
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

class TheCrewController extends AbstractController
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
     * @var CrewMemberRepository
     */
    private $crewMemberRepository;

    /**
     * @var TransportService
     */
    private $transport;

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
        CrewMemberRepository $crewMemberRepository,
        TransportService $transport,
        CacheInterface $appCache,
        LoggerInterface $logger
    )
    {
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
        $this->crewMemberRepository = $crewMemberRepository;
        $this->transport = $transport;
        $this->appCache = $appCache;
        $this->logger = $logger;
    }

    /**
     * @Route({"en": "/the-crew", "bg": "/създатели"}, name="the_crew", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if($this->checkRedirectConditions($request)) {
            return $this->redirectToSecondaryLanguageRoute($request);
        }

        $crewMembers = [];

        try {
            $crewMembers = $this->appCache->get('crew_members', function(ItemInterface $cacheItem) {
                $cacheItem->expiresAfter(DateInterval::createFromDateString(Cache::FIVE_HOURS_AS_STRING));
                return $this->crewMemberRepository->findActive();
            });
        } catch(\Exception $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
        }catch (InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage().' '.$exception->getTraceAsString());
        }

        $crewMembersFrontend = $this->transport->makeCrewMembersFrontend($crewMembers, $request->getLocale());

        $response = new Response($this->renderView('the-crew/the-crew.html.twig', [
            'crew_members' => $crewMembersFrontend
        ]));
        $response->headers->set('Content-Language', $request->attributes->get('_locale'));
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setSharedMaxAge(Cache::ONE_HOUR_IN_SECONDS);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}