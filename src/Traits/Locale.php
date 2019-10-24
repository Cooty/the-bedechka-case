<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;

trait Locale
{
    /**
     * @param Request $request
     * @param string $locale
     * @return bool
     */
    public function detectSupportForLocale(Request $request, string $locale): bool
    {
        $pattern = '/'.$locale.'/i';
        $acceptLanguage = $request->headers->get('accept-language');
        $matches = preg_match($pattern, $acceptLanguage);

        return (bool)$matches;
    }

}