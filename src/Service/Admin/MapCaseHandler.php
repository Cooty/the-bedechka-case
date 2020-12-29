<?php

namespace App\Service\Admin;

use App\Model\Transport\LatLongCords;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MapCaseHandler extends AbstractEntityHandler
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
     * @param string $googleMapsURL
     * @return LatLongCords
     * @throws Exception
     */
    private function makeLatLongFromGoogleMapsURL(string $googleMapsURL): LatLongCords
    {
        preg_match('/@(.*),(.*),/', $googleMapsURL, $matches);

        if(count($matches) !== 3) {
            throw new Exception('Longitude and Latitude was not found in the URL. Are your sure it\'s a valid Google Maps URL?');
        }

        return new LatLongCords($matches[1], $matches[2]);
    }

    /**
     * @param array $params
     * @throws Exception
     */
    public function getEntity(array $params)
    {
        try {
            /** @var UploadedFile $imageFile */
            $imageFile = $this->form->get('image')->getData();

            if($imageFile) {
                $newFileName = $this->fileUploadService->makeFilename($imageFile);

                $imageFile->move(
                    $params['public_path'].$params['upload_path'],
                    $newFileName
                );

                $this->entity->setPictureURL('/'.$params['upload_path'].$newFileName);
            }

            $latLong = $this->makeLatLongFromGoogleMapsURL(
                $this->form->get('google_maps_url')->getData());

            $this->entity->setLatitude($latLong->getLatitude());
            $this->entity->setLongitude($latLong->getLongitude());

            return $this->entity;
        } catch (Exception $exception) {
            throw $exception;
        }

    }
}