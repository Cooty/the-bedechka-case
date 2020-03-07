<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Exception;

trait Timestampable
{
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @ORM\PrePersist()
     * @throws Exception
     */
    public function setTimestampsOnPersist(): void
    {
        $now = new \DateTime();
        $this->setUpdatedAt($now);

        if($this->getCreatedAt() === null) {
            $this->setCreatedAt($now);
        }
    }
}