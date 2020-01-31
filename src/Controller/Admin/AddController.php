<?php

namespace App\Controller\Admin;

use App\Entity\LatLongCords;
use App\Traits\Admin\Security\PasswordChange;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\MapCase;
use App\Form\Admin\MapCaseForm;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AddController extends AbstractController
{
    use PasswordChange;

    /**
     * @var string
     */
    private $pswChangeSessionKey;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        string $pswChangeSessionKey,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->pswChangeSessionKey = $pswChangeSessionKey;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    private function makeLatLongFromGoogleMapsURL(string $googleMapsURL): LatLongCords
    {
        preg_match('/@(.*),(.*),/', $googleMapsURL, $matches);
        $latLong = new LatLongCords($matches[1], $matches[2]);

        return $latLong;
    }

    /**
     * @Route("/add/{entityName}/", name="admin_entity_add")
     * @param Request $request
     * @param string $entityName
     * @return Response
     */
    public function edit(Request $request, string $entityName): Response
    {
        // TODO: Figure out a way where we don't have to copy-paste this logic to all admin controllers! Events?
        if($this->checkForPasswordChangeSession($request)) {
            return $this->redirectToPasswordChange();
        }

        // TODO: Clean this up
        if($entityName === 'cases') {
            $entity = new MapCase();
            $form = $this->formFactory->create(MapCaseForm::class, $entity);
            $displayName = 'map cases';
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('image')->getData();

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('map_images_directory'),
                        $newFilename
                    );
                    $entity->setPictureURL($newFilename);
                } catch (FileException $e) {
                    $this->addFlash(
                        'warning',
                        "The file ".$imageFile->getClientOriginalName()." could not be uploaded!");
                }

                $latLong = $this->makeLatLongFromGoogleMapsURL(
                    $form->get('google_maps_url')->getData());

                $entity->setLatitude($latLong->getLatitude());
                $entity->setLongitude($latLong->getLongitude());
            }
        }
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->addFlash('success', "The new $displayName has been created!");

            return $this->redirectToRoute('admin_entity_list', ['entityName' => $entityName]);
        }

        return $this->render('admin/entity/add.html.twig', [
            'form' => $form->createView(),
            'entityDisplayName' => $displayName
        ]);
    }
}