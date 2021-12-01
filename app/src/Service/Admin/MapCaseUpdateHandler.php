<?php

namespace App\Service\Admin;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Exception;

class MapCaseUpdateHandler extends AbstractEntityHandler
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
     * @throws Exception
     */
    public function getEntity()
    {
        try {
            /** @var UploadedFile $imageFile */
            $imageFile = $this->form->get('image')->getData();
            if($imageFile) {
                $publicPath = $this->fileUploadService->moveImageAndGetPublicPath($imageFile);
                $this->entity->setPictureURL($publicPath);
            }

            return $this->entity;
        } catch (Exception $exception) {
            throw $exception;
        }

    }
}