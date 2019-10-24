<?php

namespace App\Event;

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

    private $secondaryLocale = 'bg';
    /**
     * @var array
     */
    private $supportedLocales;

    public function __construct(array $supportedLocales, string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
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
        $preferredLocale = strtolower((string)$request->get('preferred_locale'));

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

        dump($request->hasPreviousSession());

        if ($request->hasPreviousSession()) {
            return;
        }

        dump($this->getPreferredLocale($request));

        if($preferredLocale = $this->getPreferredLocale($request)) {
            $session = new Session();
            $session->set('_preferred_locale', $this->secondaryLocale);
            dump($session);
            $request->setSession($session);
//            $request->getSession()
//                ->set('_preferred_locale', $this->secondaryLocale);
        }
    }

}