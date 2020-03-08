<?php

namespace App\Service\Admin;

use \Exception;

class NewsHandler extends AbstractEntityHandler
{
    private function getDomainFromURL(string $url): string
    {
        $parse = parse_url($url);

        return $parse['host'];
    }

    /**
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function getEntity(array $params)
    {
        try {
            $source = $this->form->get('source')->getData();

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