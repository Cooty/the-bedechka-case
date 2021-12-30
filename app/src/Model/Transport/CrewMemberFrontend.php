<?php

namespace App\Model\Transport;

class CrewMemberFrontend
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $link;

    /**
     * @var string
     */
    private $pictureUrl;

    /**
     * @var string|null
     */
    private $secondPictureUrl;

    /**
     * @var string|null
     */
    private $linkLabel;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getPictureUrl(): string
    {
        return $this->pictureUrl;
    }

    /**
     * @param string $pictureUrl
     */
    public function setPictureUrl(string $pictureUrl): void
    {
        $this->pictureUrl = $pictureUrl;
    }

    /**
     * @return string|null
     */
    public function getSecondPictureUrl(): ?string
    {
        return $this->secondPictureUrl;
    }

    /**
     * @param string|null $secondPictureUrl
     */
    public function setSecondPictureUrl(?string $secondPictureUrl): void
    {
        $this->secondPictureUrl = $secondPictureUrl;
    }

    /**
     * @return string|null
     */
    public function getLinkLabel(): ?string
    {
        return $this->linkLabel;
    }

    /**
     * @param string|null $linkLabel
     */
    public function setLinkLabel(?string $linkLabel): void
    {
        $this->linkLabel = $linkLabel;
    }
}