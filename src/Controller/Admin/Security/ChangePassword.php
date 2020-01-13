<?php

namespace App\Controller\Admin\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Admin\Security\ChangePassword as ChangePasswordForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;
use App\Event\Admin\Security\ChangePassword as ChangePasswordEvent;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class ChangePassword extends AbstractController
{
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

        if($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);
            $entityManager->flush();

            $userEntity = $security->getToken()->getUser();

            $passwordChangeEvent = new ChangePasswordEvent($userEntity);

            $dispatcher->dispatch($passwordChangeEvent);

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/security/change-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}