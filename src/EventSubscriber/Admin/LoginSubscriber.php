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
use App\Enum\Admin\FlashMessages;
use App\Enum\Admin\FlashTypes;

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
     * @var string
     */
    private $pswChangeSessionKey;

    /**
     * @param Security $security
     * @param RouterInterface $router
     * @param EventDispatcherInterface $dispatcher
     * @param UserUtil $userUtil
     * @param string $pswChangeSessionKey
     */
    public function __construct(
        Security $security,
        RouterInterface $router,
        EventDispatcherInterface $dispatcher,
        UserUtil $userUtil,
        string $pswChangeSessionKey
    )
    {
        $this->security = $security;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->userUtil = $userUtil;
        $this->pswChangeSessionKey = $pswChangeSessionKey;
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
        $session = $event->getRequest()->getSession();
        $session->set($this->pswChangeSessionKey, true);

        $route = $this->router->generate('admin_security_change_password');
        $response = new RedirectResponse($route);
        $session->getFlashBag()->add(FlashTypes::INFO, FlashMessages::PASSWORD_CHANGE_REQUIRED);

        $event->setResponse($response);
    }
}