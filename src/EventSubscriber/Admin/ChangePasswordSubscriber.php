<?php

namespace App\EventSubscriber\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\Admin\Security\ChangePassword;

class ChangePasswordSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ChangePassword::NAME => 'onChangePassword'
        ];
    }

    public function onChangePassword(ChangePassword $event)
    {
        $user = $event->getAdminUser();
        $now = new \DateTime();
        $user->setLastLogin($now->getTimestamp());

        $this->manager->persist($user);
        $this->manager->flush();
    }
}