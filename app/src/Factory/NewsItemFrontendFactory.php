<?php

namespace App\Factory;

use App\Entity\News;
use App\Model\Transport\NewsItemFrontend;

class NewsItemFrontendFactory
{
    /**
     * @var string
     */
    private $secondaryLocale;

    public function __construct(string $secondaryLocale)
    {
        $this->secondaryLocale = $secondaryLocale;
    }

    public function create(News $news, string $locale): NewsItemFrontend
    {
        $dateFormat = $locale === $this->secondaryLocale ? 'd.m.o' : 'd/m/o';
        $publishedDate = $news->getPublishingDate() ?
            $news->getPublishingDate()->format($dateFormat)
            :
            null;

        return new NewsItemFrontend(
            $news->getTitle(),
            $news->getLink(),
            $news->getSource(),
            $news->getPictureURL(),
            $publishedDate
        );
    }
}