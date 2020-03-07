<?php

namespace App\Service\Admin;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectService {
    const MANUAL_REDIRECT_COOKIE_NAME = 'tbc_1_redirectUri';
    const IMPLICIT_LOGOUT_COOKIE_NAME = 'tbc_1_implicitLogout';

    private function isRelativeUri(string $uri): bool
    {
        $match = preg_match("/^\/(?!www|\.|(?:http|ftp)\s?:\/\/|[A-Za-z]:\\|\/\/).*/", $uri);

        return (bool)$match;
    }

    public function validateImplicitLogout(ParameterBag $cookies): bool
    {
        $manualRedirectUri = $cookies->get(self::MANUAL_REDIRECT_COOKIE_NAME);
        $isImplicitRedirect = $cookies->get(self::IMPLICIT_LOGOUT_COOKIE_NAME);

        return $manualRedirectUri && $isImplicitRedirect && $this->isRelativeUri($manualRedirectUri);
    }

    public function redirectAfterImplicitLogout(ParameterBag $cookies): RedirectResponse
    {
        $manualRedirectUri = $cookies->get(self::MANUAL_REDIRECT_COOKIE_NAME);

        $response = new RedirectResponse($manualRedirectUri);
        $response->headers->clearCookie(self::MANUAL_REDIRECT_COOKIE_NAME);
        $response->headers->clearCookie(self::IMPLICIT_LOGOUT_COOKIE_NAME);

        return $response;
    }
}