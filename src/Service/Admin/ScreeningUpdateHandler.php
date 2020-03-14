<?php

namespace App\Service\Admin;

use \DateTime;

class ScreeningUpdateHandler extends AbstractFormHandler
{

    public function getForm()
    {
        /**
         * @var $start DateTime
         */
        $start = $this->entity->getStart();

        $hours = $start->format('G');
        $minutes = $start->format('i');

        $this->form->get('date')->setData($start);
        $this->form->get('hours')->setData($hours);
        $this->form->get('minutes')->setData($minutes);

        return $this->form;
    }
}