<?php

namespace App\Enum;

abstract class Cache
{
    const FULL_PAGE_CACHE_EXPIRATION = 3600; // 1h
    const API_RESPONSE_EXPIRATION = '5 hours';
}