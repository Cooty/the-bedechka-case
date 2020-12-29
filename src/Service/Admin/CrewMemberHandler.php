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

    public function __construct($entity, FormInterface $form, FileUploadService $fileUploadService)
    {
        parent::__construct($entity, $form);
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function getEntity(array $params)
    {
        try {
            /** @var UploadedFile $imageFile */
            $imageFile = $this->form->get('image')->getData();

            /** @var UploadedFile $imageFile */
            $secondImageFile = $this->form->get('secondImage')->getData();

            if($imageFile) {
                $newFileName = $this->fileUploadService->makeFilename($imageFile);

                $imageFile->move(
                    $params['public_path'].$params['upload_path'],
                    $newFileName
                );

                $this->entity->setPictureURL('/'.$params['upload_path'].$newFileName);

                if($secondImageFile) {
                    $secondNewFileName = $this->fileUploadService->makeFilename($secondImageFile);

                    $secondImageFile->move(
                        $params['public_path'].$params['upload_path'],
                        $secondNewFileName
                    );

                    $this->entity->setSecondPictureUrl('/'.$params['upload_path'].$secondNewFileName);
                }
            }

            return $this->entity;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}