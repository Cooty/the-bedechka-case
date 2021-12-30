<?php

namespace App\Service\Admin;

use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CrewMemberHandler extends AbstractEntityHandler
{
    /**
     * @var FileUploadService
     */
    private $fileUploadService;

    public function __construct(
        $entity,
        FormInterface $form,
        FileUploadService $fileUploadService
    )
    {
        parent::__construct($entity, $form);
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getEntity()
    {
        try {
            /** @var UploadedFile $imageFile */
            $imageFile = $this->form->get('image')->getData();

            /** @var UploadedFile $imageFile */
            $secondImageFile = $this->form->get('secondImage')->getData();

            if($imageFile) {
                $imagePublicPath = $this->fileUploadService->moveImageAndGetPublicPath($imageFile);
                $this->entity->setPictureURL($imagePublicPath);
            }

            if($secondImageFile) {
                $secondImagePublicPath = $this->fileUploadService->moveImageAndGetPublicPath($secondImageFile);
                $this->entity->setSecondPictureUrl($secondImagePublicPath);
            }

            return $this->entity;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}