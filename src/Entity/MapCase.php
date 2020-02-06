<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MapCaseRepository")
 */
class MapCase
{
    const URL_PARAM_NAME = 'cases';
    const DISPLAY_NAME = 'map cases';

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
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank()
     */
    private $nameEN;

    /**
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank()
     */
    private $nameBG;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank()
     */
    private $descriptionEN;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank()
     */
    private $descriptionBG;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Assert\Url(message="Please enter a valid web address")
     */
    private $link;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type("float", message="Longitude has to be a decimal number")
     */
    private $longitude;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type("float", message="Latitude has to be a decimal number")
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $pictureURL;

    /**
     * @var bool
     */
    private $archived = false;

    public function getId(): UuidInterface
    {
        return $this->id;
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

    public function getDescriptionEN(): ?string
    {
        return $this->descriptionEN;
    }

    public function setDescriptionEN(string $descriptionEN): self
    {
        $this->descriptionEN = $descriptionEN;

        return $this;
    }

    public function getDescriptionBG(): ?string
    {
        return $this->descriptionBG;
    }

    public function setDescriptionBG(string $descriptionBG): self
    {
        $this->descriptionBG = $descriptionBG;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getPictureURL(): ?string
    {
        return $this->pictureURL;
    }

    public function setPictureURL(?string $pictureURL): self
    {
        $this->pictureURL = $pictureURL;

        return $this;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     */
    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }
}
