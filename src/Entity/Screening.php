<?php

namespace App\Entity;

use App\Traits\Archivable;
use App\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScreeningRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Screening
{
    use Timestampable;
    use Archivable;

    const URL_PARAM_NAME = 'screenings';
    const DISPLAY_NAME = 'screening';

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nameEN;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nameBG;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $venueNameEN;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $venueNameBG;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $venueLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $eventLink;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getNameEN(): ?string
    {
        return $this->nameEN;
    }

    public function setNameEN(string $nameEN): self
    {
        $this->nameEN = $nameEN;

        return $this;
    }

    public function getNameBG(): ?string
    {
        return $this->nameBG;
    }

    public function setNameBG(string $nameBG): self
    {
        $this->nameBG = $nameBG;

        return $this;
    }

    public function getVenueNameEN(): ?string
    {
        return $this->venueNameEN;
    }

    public function setVenueNameEN(string $venueNameEN): self
    {
        $this->venueNameEN = $venueNameEN;

        return $this;
    }

    public function getVenueNameBG(): ?string
    {
        return $this->venueNameBG;
    }

    public function setVenueNameBG(string $venueNameBG): self
    {
        $this->venueNameBG = $venueNameBG;

        return $this;
    }

    public function getVenueLink(): ?string
    {
        return $this->venueLink;
    }

    public function setVenueLink(?string $venueLink): self
    {
        $this->venueLink = $venueLink;

        return $this;
    }

    public function getEventLink(): ?string
    {
        return $this->eventLink;
    }

    public function setEventLink(?string $eventLink): self
    {
        $this->eventLink = $eventLink;

        return $this;
    }
}
