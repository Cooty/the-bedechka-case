<?php

namespace App\Factory;

use App\Entity\News;
use App\Model\Transport\NewsItemFrontend;

class NewsItemFrontendFactory
{
    public function create(News $news): NewsItemFrontend
    {
        return new NewsItemFrontend(
            $news->getTitle(),
            $news->getLink(),
            $news->getSource(),
            $news->getPictureURL()
        );
    }
}