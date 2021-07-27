<?php

namespace App\Model\Transport;

class NewsItemFrontend
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string|null
     */
    private $source;

    /**
     * @var string|null
     */
    private $image;

    /**
     * @var string|null
     */
    private $publishingDate;

    public function __construct(
        string $title,
        string $link,
        ?string $source,
        ?string $image,
        ?string $publishingDate
    )
    {
        $this->title = $title;
        $this->link = $link;
        $this->source = $source;
        $this->image = $image;
        $this->publishingDate = $publishingDate;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @return string|null
     */
    public function getPublishingDate(): ?string
    {
        return $this->publishingDate;
    }

    /**
     * @param string|null $publishingDate
     */
    public function setPublishingDate(?string $publishingDate): void
    {
        $this->publishingDate = $publishingDate;
    }
}