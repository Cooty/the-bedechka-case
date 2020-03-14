<?php

namespace App\Service\Admin;

use Exception;
use \DateTime;
use App\Util\TimeUtil;

class ScreeningHandler extends AbstractEntityHandler
{
    private function getISO8601String(DateTime $date, int $hours, int $minutes): string
    {
        $date = $date->format('Y-m-d');
        $hours = TimeUtil::addLeadingZero($hours);
        $minutes = TimeUtil::addLeadingZero($minutes);

        return sprintf("%sT%s:%sZ", $date, $hours, $minutes);
    }

    /**
     * @param array $params
     * @throws Exception
     */
    public function getEntity(array $params)
    {
        try {
            $date = $this->form->get('date')->getData();
            $hours = $this->form->get('hours')->getData();
            $minutes = $this->form->get('minutes')->getData();

            $iso8601String = $this->getISO8601String($date, $hours, $minutes);
            $start = new DateTime($iso8601String);

            $this->entity->setStart($start);

            return $this->entity;

        } catch (Exception $e) {
            throw $e;
        }
    }
}