<?php

namespace App\Controller\Admin\Security;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Security\ChangePassword as ChangePasswordForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use App\Event\Admin\Security\ChangePassword as ChangePasswordEvent;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class ChangePassword extends AbstractAdminController
{
    private function handleForm(
        UserPasswordEncoderInterface $passwordEncoder,
        UserInterface $user,
        CoreSecurity $security,
        EventDispatcherInterface $dispatcher,
        SessionInterface $session
    ): RedirectResponse
    {
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($user);
        $entityManager->flush();

        /**
         * @var $userEntity User|null
         */
        $userEntity = $security->getToken()->getUser();
        $userEntity->eraseCredentials();

        $passwordChangeEvent = new ChangePasswordEvent($userEntity);
        $session->set($this->pswChangeSessionKey, false);

        $dispatcher->dispatch($passwordChangeEvent);

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * @Route("/change-password", name="admin_security_change_password")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EventDispatcherInterface $dispatcher
     * @param Request $request
     * @param CoreSecurity $security
     * @return Response
     */
    public function changePassword(
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $dispatcher,
        Request $request,
        CoreSecurity $security
    ): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(ChangePasswordForm::class, $user);
        $form->handleRequest($request);
        $session = $request->getSession();

        if($form->isSubmitted() && $form->isValid()) {
            return $this->handleForm($passwordEncoder, $user, $security, $dispatcher, $session);
        }

        return $this->render('admin/security/change-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}