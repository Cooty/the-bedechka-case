<?php

namespace App\Service\Admin;

use \Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NewsHandler extends AbstractEntityHandler
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

    private function getDomainFromURL(string $url): string
    {
        return parse_url($url)['host'];
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getEntity()
    {
        try {
            $source = $this->form->get('source')->getData();
            /** @var UploadedFile $imageFile */
            $imageFile = $this->form->get('image')->getData();
            $publicPath = $this->fileUploadService->moveImageAndGetPublicPath($imageFile);
            $this->entity->setLogoURL($publicPath);

            if(empty($source)) {
                $url = $this->form->get('link')->getData();
                $source = $this->getDomainFromURL($url);

                $this->entity->setSource($source);
            }

            return $this->entity;
        } catch(Exception $exception) {
            throw $exception;
        }

    }
}