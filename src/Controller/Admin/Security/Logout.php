<?php

namespace App\Controller\Admin\Security;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class Logout
{
    /**
     * @Route("/logout", name="admin_security_logout")
     */
    public function logout()
    {
        // empty function - Symfony will do all the work just need a dummy action for the route,
        // we've set in security.yml as the "logout -> path"
    }
}