<?php

namespace App\EventSubscriber\Admin;

use App\Entity\User;
use App\Util\UserUtil;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Security;

class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var UserUtil
     */
    private $userUtil;

    /**
     * AdminLoginListener constructor.
     * @param Security $security
     * @param RouterInterface $router
     * @param EventDispatcherInterface $dispatcher
     * @param UserUtil $userUtil
     */
    public function __construct(
        Security $security,
        RouterInterface $router,
        EventDispatcherInterface $dispatcher,
        UserUtil $userUtil
    )
    {
        $this->security = $security;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->userUtil = $userUtil;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [
                'onInteractiveLogin'
            ]
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        if($this->security->isGranted(User::ROLE_ADMIN)) {
            /**
             * @var User $user
             */
            $user = $event->getAuthenticationToken()->getUser();

            if($user->getLastLogin() === null) {
                $this->dispatcher->addListener(KernelEvents::RESPONSE, [
                    $this,
                    'onKernelResponse'
                ]);
            } else {
                $this->userUtil->updateLastLogin($user);
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event) {
        $response = new RedirectResponse(
            $this->router->generate('admin_security_change_password'));

        $event->setResponse($response);
    }
}