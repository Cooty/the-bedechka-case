<?php

namespace App\Service\Admin;

use Exception;
use \DateTime;
use App\Util\TimeUtil;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ScreeningHandler extends AbstractEntityHandler
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

    private function getISO8601String(DateTime $date, int $hours, int $minutes): string
    {
        $date = $date->format('Y-m-d');
        $hours = TimeUtil::addLeadingZero($hours);
        $minutes = TimeUtil::addLeadingZero($minutes);

        return sprintf("%sT%s:%sZ", $date, $hours, $minutes);
    }

    /**
     * @throws Exception
     */
    public function getEntity()
    {
        try {
            $date = $this->form->get('date')->getData();
            $hours = $this->form->get('hours')->getData();
            $minutes = $this->form->get('minutes')->getData();

            $iso8601String = $this->getISO8601String($date, $hours, $minutes);
            $start = new DateTime($iso8601String);

            $this->entity->setStart($start);

            /** @var UploadedFile $imageFile */
            $imageFile = $this->form->get('image')->getData();
            if($imageFile) {
                $publicPath = $this->fileUploadService->moveImageAndGetPublicPath($imageFile);
                $this->entity->setPictureURL($publicPath);
            }

            return $this->entity;

        } catch (Exception $e) {
            throw $e;
        }
    }
}