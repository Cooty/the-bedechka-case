<?php

namespace App\Util;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserUtil
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function updateLastLogin(User $user): User
    {
        $now = new \DateTime('now');
        $user->setLastLogin($now);

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }
}