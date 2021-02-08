<?php

namespace App\Service\Admin;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService
{
    /**
     * @var string
     */
    private $userImagesDirectory;

    /**
     * @var string
     */
    private $publicDirectoryPath;

    public function __construct(
        string $userImagesDirectory,
        string $publicDirectoryPath
    )
    {
        $this->userImagesDirectory = $userImagesDirectory;
        $this->publicDirectoryPath = $publicDirectoryPath;
    }

    private function makeFilename(UploadedFile $imageFile): string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename);
        return $safeFilename.'-'.uniqid().'.'.'jpg';
    }

    public function moveImageAndGetPublicPath(?UploadedFile $imageFile): ?string
    {
        if($imageFile) {
            $newFileName = $this->makeFilename($imageFile);

            $imageFile->move(
                $this->publicDirectoryPath.$this->userImagesDirectory,
                $newFileName
            );

            return '/'.$this->userImagesDirectory.$newFileName;
        }

        return null;
    }
}