<?php

namespace App\Service;

use App\Entity\MapCase;
use App\Entity\News;
use App\Entity\Screening;
use App\Entity\Transport\LatLongCords;
use App\Entity\Transport\MapCaseFrontend;
use App\Entity\Transport\NewsItemFrontend;
use App\Entity\Transport\ScreeningFrontend;

class TransportService
{
    const BULGARIAN = 'bg';

    private function makeMapCaseFrontend(MapCase $case, string $locale): MapCaseFrontend
    {
        $frontendCase = new MapCaseFrontend();
        $frontendCase->setId($case->getId());

        $name = $locale === self::BULGARIAN ? $case->getNameBG() : $case->getNameEN();
        $frontendCase->setName($name);

        $description = $locale === self::BULGARIAN ? $case->getDescriptionBG() : $case->getDescriptionEN();
        $frontendCase->setDescription($description);

        $coords = new LatLongCords($case->getLatitude(), $case->getLongitude());
        $frontendCase->setCoords($coords);

        $frontendCase->setLink($case->getLink());
        $frontendCase->setImage($case->getPictureURL());

        return $frontendCase;
    }

    /**
     * @param MapCase[] $cases
     * @param string $locale
     * @return MapCaseFrontend[]
     */
    public function makeMapCasesFrontend(array $cases, string $locale): array
    {
        return array_map(function($case) use ($locale) {
            return $this->makeMapCaseFrontend($case, $locale);
        }, $cases);
    }

    private function makeNewsItemFrontend(News $news): NewsItemFrontend
    {
        return new NewsItemFrontend(
            $news->getTitle(),
            $news->getLink(),
            $news->getSource()
        );
    }

    /**
     * @param News[] $news
     * @return NewsItemFrontend[]
     */
    public function makeNewsItemsFrontend(array $news): array
    {
        return array_map(function($n) {
            return $this->makeNewsItemFrontend($n);
        }, $news);
    }


    private function makeScreeningFrontend(Screening $screening, string $locale): ScreeningFrontend
    {
        $screeningFrontend = new ScreeningFrontend();

        $name = $locale === self::BULGARIAN ? $screening->getNameBG() : $screening->getNameEN();
        $screeningFrontend->setName($name);

        $venueName = $locale === self::BULGARIAN ? $screening->getVenueNameBG() : $screening->getVenueNameEN();
        $screeningFrontend->setVenueName($venueName);

        $screeningFrontend->setEventLink($screening->getEventLink());

        $screeningFrontend->setVenueLink($screening->getVenueLink());

        $screeningFrontend->setStart($screening->getStart());

        return $screeningFrontend;
    }

    /**
     * @param Screening[] $screenings
     * @param string $locale
     * @return ScreeningFrontend[]
     */
    public function makeScreeningsFrontend(array $screenings, string $locale): array
    {
        return array_map(function($s) use ($locale) {
            return $this->makeScreeningFrontend($s, $locale);
        }, $screenings);
    }
}