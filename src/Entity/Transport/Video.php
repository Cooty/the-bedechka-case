<?php

namespace App\Entity\Transport;

class Video
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $titleBg;

    /**
     * @var VideoThumbnails
     */
    private $thumbnails;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $descriptionBg;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return VideoThumbnails
     */
    public function getThumbnails(): VideoThumbnails
    {
        return $this->thumbnails;
    }

    /**
     * @param VideoThumbnails $thumbnails
     */
    public function setThumbnails(VideoThumbnails $thumbnails): void
    {
        $this->thumbnails = $thumbnails;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTitleBg(): string
    {
        return $this->titleBg;
    }

    /**
     * @param string $titleBg
     */
    public function setTitleBg(string $titleBg): void
    {
        $this->titleBg = $titleBg;
    }

    /**
     * @return string|null
     */
    public function getDescriptionBg(): ?string
    {
        return $this->descriptionBg;
    }

    /**
     * @param string|null $descriptionBg
     */
    public function setDescriptionBg(?string $descriptionBg): void
    {
        $this->descriptionBg = $descriptionBg;
    }
}