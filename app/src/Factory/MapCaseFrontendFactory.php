<?php

namespace App\Factory;

use App\Entity\MapCase;
use App\Model\Transport\LatLongCords;
use App\Model\Transport\MapCaseFrontend;

class MapCaseFrontendFactory
{
    /**
     * @var string
     */
    private $secondaryLocale;

    public function __construct(string $secondaryLocale)
    {
        $this->secondaryLocale = $secondaryLocale;
    }

    public function create(MapCase $case, string $locale): MapCaseFrontend
    {
        $frontendCase = new MapCaseFrontend();
        $frontendCase->setId($case->getId());

        $name = $locale === $this->secondaryLocale ? $case->getNameBG() : $case->getNameEN();
        $frontendCase->setName($name);

        $description = $locale === $this->secondaryLocale ? $case->getDescriptionBG() : $case->getDescriptionEN();
        $frontendCase->setDescription($description);

        $coords = new LatLongCords($case->getLatitude(), $case->getLongitude());
        $frontendCase->setCoords($coords);

        $frontendCase->setLink($case->getLink());
        $frontendCase->setImage($case->getPictureURL());

        return $frontendCase;
    }
}