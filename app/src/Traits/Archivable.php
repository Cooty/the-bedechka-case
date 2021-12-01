<?php


namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Archivable
{
    /**
     * @ORM\Column(type="boolean")
     */
    private $archived = false;

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     */
    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }
}