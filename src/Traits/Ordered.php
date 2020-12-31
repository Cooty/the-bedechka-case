<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Ordered
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderOfAppearance;

    /**
     * @return int|null
     */
    public function getOrderOfAppearance(): ?int
    {
        return $this->orderOfAppearance;
    }

    /**
     * @param int|null $orderOfAppearance
     */
    public function setOrderOfAppearance(?int $orderOfAppearance): void
    {
        $this->orderOfAppearance = $orderOfAppearance;
    }

}