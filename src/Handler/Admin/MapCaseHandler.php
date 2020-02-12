<?php

namespace App\Handler\Admin;

use App\Entity\LatLongCords;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MapCaseHandler extends AbstractEntityHandler
{
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

        $latLong = new LatLongCords($matches[1], $matches[2]);

        return $latLong;
    }

    private function makeFilename(UploadedFile $imageFile): string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

        return $newFilename;
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

            $newFileName = $this->makeFilename($imageFile);

            $imageFile->move(
                $params['upload_path'],
                $newFileName
            );

            $this->entity->setPictureURL('/map-images/'.$newFileName);

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