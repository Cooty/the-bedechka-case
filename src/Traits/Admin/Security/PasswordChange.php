<?php

namespace App\Traits\Admin\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

trait PasswordChange
{
    private function checkForPasswordChangeSession(Request $request): bool
    {
        $session = $request->getSession();

        if($session->get($this->pswChangeSessionKey)) {
            return true;
        } else {
            return false;
        }
    }

    public function redirectToPasswordChange(): RedirectResponse
    {
        return $this->redirectToRoute('admin_security_change_password');
    }

}