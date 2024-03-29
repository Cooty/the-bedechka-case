<?php

namespace App\Entity;

use App\Traits\Archivable;
use App\Traits\Timestampable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class News
{
    use Timestampable;
    use Archivable;

    const URL_PARAM_NAME = 'news';
    const DISPLAY_NAME = 'news item';

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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=400)
     * @Assert\Url(message="Please enter a valid web address")
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $pictureURL;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishingDate;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPictureURL(): ?string
    {
        return $this->pictureURL;
    }

    /**
     * @param string|null $pictureURL
     */
    public function setPictureURL(?string $pictureURL): void
    {
        $this->pictureURL = $pictureURL;
    }

    public function getPublishingDate(): ?DateTimeInterface
    {
        return $this->publishingDate;
    }

    /**
     * @param DateTimeInterface|null $publishingDate
     */
    public function setPublishingDate(?DateTimeInterface $publishingDate): void
    {
        $this->publishingDate = $publishingDate;
    }
}
