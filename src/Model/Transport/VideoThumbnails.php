<?php

namespace App\Model\Transport;

class VideoThumbnails
{
    /**
     * @var string
     */
    private $mobile;

    /**
     * @var string
     */
    private $tablet;

    /**
     * @var string
     */
    private $desktop;

    /**
     * @return string
     */
    public function getMobile(): string
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getTablet(): string
    {
        return $this->tablet;
    }

    /**
     * @param string $tablet
     */
    public function setTablet(string $tablet): void
    {
        $this->tablet = $tablet;
    }

    /**
     * @return string
     */
    public function getDesktop(): string
    {
        return $this->desktop;
    }

    /**
     * @param string $desktop
     */
    public function setDesktop(string $desktop): void
    {
        $this->desktop = $desktop;
    }
}