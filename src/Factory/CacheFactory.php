<?php

namespace App\Factory;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheFactory
{
    public function create() {
        return new FilesystemAdapter();
    }
}