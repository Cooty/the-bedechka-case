<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private $secondaryLocale;

    /**
     * @var array
     */
    private $supportedLocales;

    /**
     * @var string
     */
    private $languageSettingParamName;

    /**
     * @var string
     */
    private $languageSettingSessionKey;

    public function __construct(
        array $supportedLocales,
        string $defaultLocale,
        string $secondaryLocale,
        string $languageSettingParamName,
        string $languageSettingSessionKey
    ) {
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
        $this->secondaryLocale = $secondaryLocale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->languageSettingSessionKey = $languageSettingSessionKey;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                'onKernelRequest',
                20
            ]
        ];
    }

    private function getPreferredLocale(Request $request): string
    {
        $preferredLocale = strtolower((string)$request->get($this->languageSettingParamName));

        if(in_array($preferredLocale, $this->supportedLocales)) {
            return $preferredLocale;
        } else {
            return '';
        }

    }

    public function onKernelRequest(RequestEvent $event)
    {
        /**
         * @var $request Request
         */
        $request = $event->getRequest();

        if($preferredLocale = $this->getPreferredLocale($request)) {
            if(!$request->hasPreviousSession()) {
                $session = new Session();
            } else {
                $session = $request->getSession();
            }
            $session->set($this->languageSettingSessionKey, $this->secondaryLocale);
            $request->setSession($session);
        }
    }

}