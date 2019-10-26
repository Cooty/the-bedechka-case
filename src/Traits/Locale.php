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

    public function hasPreferredLanguageSet(Request $request): bool
    {
        $session = $request->getSession();

        return (bool)strtolower((string)$request->get($this->languageSettingParamName)) ||
            ($session && $session->get($this->languageSettingSessionKey));
    }

    public function isSecondaryLocale(Request $request): bool
    {
        return $request->getLocale() === $this->secondaryLocale;
    }

    public function redirectToSecondaryLanguageRoute($request)
    {
        $routeName = $request->attributes->get('_route');
        return $this->redirectToRoute($routeName, ['_locale'=>$this->secondaryLocale]);
    }

    public function checkRedirectConditions(Request $request) {
        if($this->detectSupportForLocale($request, $this->secondaryLocale) &&
            !$this->hasPreferredLanguageSet($request) &&
            !$this->isSecondaryLocale($request)
        ) {
            return true;
        } else {
            return false;
        }
    }

}