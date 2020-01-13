<?php

namespace App\EventSubscriber\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * AdminLoginListener constructor.
     * @param Security $security
     * @param EntityManagerInterface $manager
     * @param RouterInterface $router
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        Security $security,
        EntityManagerInterface $manager,
        RouterInterface $router,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->security = $security;
        $this->manager = $manager;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
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
                dump('last login is null');
                $this->dispatcher->addListener(KernelEvents::RESPONSE, [
                    $this,
                    'onKernelResponse'
                ]);
            } else {
                $now = new \DateTime();
                $user->setLastLogin($now->getTimestamp());

                $this->manager->persist($user);
                $this->manager->flush();
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event) {
        $response = new RedirectResponse(
            $this->router->generate('admin_security_change_password'));

        $event->setResponse($response);
    }
}