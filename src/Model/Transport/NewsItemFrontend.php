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

    public function __construct(string $title, string $link, string $source)
    {
        $this->title = $title;
        $this->link = $link;
        $this->source = $source;
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
}