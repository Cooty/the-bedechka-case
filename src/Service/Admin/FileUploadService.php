<?php

namespace App\Service\Admin;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService
{
    public function makeFilename(UploadedFile $imageFile): string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename);
        return $safeFilename.'-'.uniqid().'.'.'jpg';
    }
}