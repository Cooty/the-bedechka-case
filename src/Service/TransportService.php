<?php

namespace App\Service;

use App\Entity\MapCase;
use App\Entity\Transport\LatLongCords;
use App\Entity\Transport\MapCaseFrontend;

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

    public function makeMapCasesFrontend(array $cases, string $locale): array
    {
        return array_map(function($case) use ($cases, $locale) {
            return $this->makeMapCaseFrontend($case, $locale);
        }, $cases);
    }
}