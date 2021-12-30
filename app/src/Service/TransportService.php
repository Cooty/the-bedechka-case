<?php

namespace App\Service;

use App\Entity\CrewMember;
use App\Entity\MapCase;
use App\Entity\News;
use App\Entity\Screening;
use App\Factory\CrewMemberFrontendFactory;
use App\Model\Transport\CrewMemberFrontend;
use App\Model\Transport\MapCaseFrontend;
use App\Model\Transport\NewsItemFrontend;
use App\Model\Transport\ScreeningFrontend;
use App\Factory\MapCaseFrontendFactory;
use App\Factory\NewsItemFrontendFactory;
use App\Factory\ScreeningFrontendFactory;

class TransportService
{
    /**
     * @var NewsItemFrontendFactory
     */
    private $newsItemFrontend;

    /**
     * @var MapCaseFrontendFactory
     */
    private $mapCaseFrontend;

    /**
     * @var ScreeningFrontendFactory
     */
    private $screeningFrontend;

    /**
     * @var CrewMemberFrontendFactory
     */
    private $crewMemberFrontend;

    public function __construct(
        NewsItemFrontendFactory $newsItemFrontend,
        MapCaseFrontendFactory $mapCaseFrontend,
        ScreeningFrontendFactory $screeningFrontend,
        CrewMemberFrontendFactory $crewMemberFrontend
    )
    {
        $this->newsItemFrontend = $newsItemFrontend;
        $this->mapCaseFrontend = $mapCaseFrontend;
        $this->screeningFrontend = $screeningFrontend;
        $this->crewMemberFrontend = $crewMemberFrontend;
    }

    /**
     * @param MapCase[] $cases
     * @param string $locale
     * @return MapCaseFrontend[]
     */
    public function makeMapCasesFrontend(array $cases, string $locale): array
    {
        return array_map(function($case) use ($locale) {
            return $this->mapCaseFrontend->create($case, $locale);
        }, $cases);
    }

    /**
     * @param News[] $news
     * @param string $locale
     * @return NewsItemFrontend[]
     */
    public function makeNewsItemsFrontend(array $news, string $locale = 'en'): array
    {
        return array_map(function($n) use ($locale) {
            return $this->newsItemFrontend->create($n, $locale);
        }, $news);
    }

    /**
     * @param Screening[] $screenings
     * @param string $locale
     * @return ScreeningFrontend[]
     */
    public function makeScreeningsFrontend(array $screenings, string $locale): array
    {
        return array_map(function($s) use ($locale) {
            return $this->screeningFrontend->create($s, $locale);
        }, $screenings);
    }

    /**
     * @param CrewMember[] $crewMembers
     * @param string $locale
     * @return CrewMemberFrontend[]
     */
    public function makeCrewMembersFrontend(array $crewMembers, string $locale): array
    {
        return array_map(function($cm) use ($locale) {
            return $this->crewMemberFrontend->create($cm, $locale);
        }, $crewMembers);
    }
}