<?php

namespace App\Entity\Transport;

use DateTimeInterface;

class ScreeningFrontend
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $venueName;

    /**
     * @var string|null
     */
    private $venueLink;

    /**
     * @var string|null
     */
    private $eventLink;

    /**
     * @var DateTimeInterface
     */
    private $start;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getVenueName(): string
    {
        return $this->venueName;
    }

    /**
     * @param string $venueName
     */
    public function setVenueName(string $venueName): void
    {
        $this->venueName = $venueName;
    }

    /**
     * @return string|null
     */
    public function getVenueLink(): ?string
    {
        return $this->venueLink;
    }

    /**
     * @param string|null $venueLink
     */
    public function setVenueLink(?string $venueLink): void
    {
        $this->venueLink = $venueLink;
    }

    /**
     * @return string|null
     */
    public function getEventLink(): ?string
    {
        return $this->eventLink;
    }

    /**
     * @param string|null $eventLink
     */
    public function setEventLink(?string $eventLink): void
    {
        $this->eventLink = $eventLink;
    }

    /**
     * @return DateTimeInterface
     */
    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    /**
     * @param DateTimeInterface $start
     */
    public function setStart(DateTimeInterface $start): void
    {
        $this->start = $start;
    }
}