<?php

namespace App\EventSubscriber\Admin;

use App\Util\UserUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\Admin\Security\ChangePassword;

class ChangePasswordSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserUtil
     */
    private $userUtil;

    public function __construct(UserUtil $userUtil)
    {
        $this->userUtil = $userUtil;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ChangePassword::class => 'onChangePassword'
        ];
    }

    public function onChangePassword(ChangePassword $event)
    {
        $user = $event->getAdminUser();

        $this->userUtil->updateLastLogin($user);
    }
}