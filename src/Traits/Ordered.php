<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Ordered
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $order;

    /**
     * @return int|null
     */
    public function getOrder(): ?int
    {
        return $this->order;
    }

    /**
     * @param int|null $order
     */
    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }

}