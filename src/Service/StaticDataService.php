<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class StaticDataService
{
    /**
     * @var string
     */
    private $staticDataDirectory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(string $staticDataDirectory, LoggerInterface $logger)
    {
        $this->staticDataDirectory = $staticDataDirectory;
        $this->logger = $logger;
    }

    public function getDataFromFile(string $fileName)
    {
        try {
            $content = file_get_contents($this->staticDataDirectory.$fileName);
            $data = json_decode($content);

            return $data;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage().' '.$e->getTraceAsString());
            return [];
        }

    }
}