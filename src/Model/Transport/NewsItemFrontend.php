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
     * @var string
     */
    private $source;

    /**
     * @var string|null
     */
    private $image;

    public function __construct(string $title, string $link, string $source, ?string $image)
    {
        $this->title = $title;
        $this->link = $link;
        $this->source = $source;
        $this->image = $image;
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
}