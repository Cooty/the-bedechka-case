<?php

namespace App\Controller\Admin;

use App\Traits\Admin\Security\PasswordChange;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class EditController extends AbstractController
{
    use PasswordChange;

    /**
     * @var string
     */
    private $pswChangeSessionKey;

    public function __construct(string $pswChangeSessionKey)
    {
        $this->pswChangeSessionKey = $pswChangeSessionKey;
    }

    /**
     * @Route("/edit/{entityName}", name="admin_entity_edit")
     * @param Request $request
     * @param string $entityName
     * @return Response
     */
    public function edit(Request $request, string $entityName): Response
    {
        if($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        return $this->render('admin/edit/index.html.twig', [
            'entityName' => $entityName,
            'entityDisplayName' => 'map cases' // TODO: Make this dynamic
            // TODO: Insert a form
        ]);
    }
}