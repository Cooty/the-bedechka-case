<?php


namespace App\Controller\Admin;


use App\Traits\Admin\Security\PasswordChange;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAdminController extends AbstractController
{
    use PasswordChange;

    /**
     * @var string
     */
    protected $pswChangeSessionKey;

    public function __construct(string $pswChangeSessionKey)
    {
        $this->pswChangeSessionKey = $pswChangeSessionKey;
    }
}