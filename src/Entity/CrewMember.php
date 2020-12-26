<?php

namespace App\Entity;

use App\Repository\CrewMemberRepository;
use App\Traits\Archivable;
use App\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CrewMemberRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class CrewMember
{
    use Timestampable;
    use Archivable;

    const URL_PARAM_NAME = 'crew-members';
    const DISPLAY_NAME = 'crew member';

    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameBg;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleEn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titleBg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Please enter a valid web address")
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pictureUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkLabelBg;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): self
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    public function getNameBg(): ?string
    {
        return $this->nameBg;
    }

    public function setNameBg(string $nameBg): self
    {
        $this->nameBg = $nameBg;

        return $this;
    }

    public function getTitleEn(): ?string
    {
        return $this->titleEn;
    }

    public function setTitleEn(string $titleEn): self
    {
        $this->titleEn = $titleEn;

        return $this;
    }

    public function getTitleBg(): ?string
    {
        return $this->titleBg;
    }

    public function setTitleBg(string $titleBg): self
    {
        $this->titleBg = $titleBg;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    public function setPictureUrl(string $pictureUrl): self
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    public function getLinkLabel(): ?string
    {
        return $this->linkLabel;
    }

    public function setLinkLabel(?string $linkLabel): self
    {
        $this->linkLabel = $linkLabel;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinkLabelBg()
    {
        return $this->linkLabelBg;
    }

    /**
     * @param mixed $linkLabelBg
     */
    public function setLinkLabelBg($linkLabelBg): void
    {
        $this->linkLabelBg = $linkLabelBg;
    }
}
