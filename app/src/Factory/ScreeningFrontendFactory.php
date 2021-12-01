<?php

namespace App\Factory;

use App\Entity\Screening;
use App\Model\Transport\ScreeningFrontend;

class ScreeningFrontendFactory
{
    /**
     * @var string
     */
    private $secondaryLocale;

    public function __construct(string $secondaryLocale)
    {
        $this->secondaryLocale = $secondaryLocale;
    }

    public function create(Screening $screening, string $locale): ScreeningFrontend
    {
        $screeningFrontend = new ScreeningFrontend();

        $name = $locale === $this->secondaryLocale ? $screening->getNameBG() : $screening->getNameEN();
        $screeningFrontend->setName($name);

        $venueName = $locale === $this->secondaryLocale ? $screening->getVenueNameBG() : $screening->getVenueNameEN();
        $screeningFrontend->setVenueName($venueName);

        $screeningFrontend->setEventLink($screening->getEventLink());

        $screeningFrontend->setVenueLink($screening->getVenueLink());

        $screeningFrontend->setStart($screening->getStart());

        if($image = $screening->getPictureURL()) {
            $screeningFrontend->setImage($image);
        }

        return $screeningFrontend;
    }
}