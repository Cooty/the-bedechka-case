<?php

namespace App\Event\Admin\Security;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ChangePassword extends Event
{
    // This is what identifies the Event throughout Symfony. Must be unique!
    const NAME = 'admin.user.change.password';

    /**
     * @var User
     */
    private $adminUser;

    public function __construct(User $adminUser)
    {
        $this->adminUser = $adminUser;
    }

    /**
     * @return User
     */
    public function getAdminUser(): User
    {
        return $this->adminUser;
    }
}