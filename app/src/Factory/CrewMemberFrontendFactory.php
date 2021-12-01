<?php

namespace App\Factory;

use App\Entity\CrewMember;
use App\Model\Transport\CrewMemberFrontend;

class CrewMemberFrontendFactory
{
    /**
     * @var string
     */
    private $secondaryLocale;

    public function __construct(string $secondaryLocale)
    {
        $this->secondaryLocale = $secondaryLocale;
    }

    public function create(CrewMember $crewMember, string $locale): CrewMemberFrontend
    {
        $crewMemberFrontend = new CrewMemberFrontend();

        $name = $locale === $this->secondaryLocale ? $crewMember->getNameBg() : $crewMember->getNameEn();
        $crewMemberFrontend->setName($name);

        $title = $locale === $this->secondaryLocale ? $crewMember->getTitleBg() : $crewMember->getTitleEn();
        $crewMemberFrontend->setTitle($title);

        if($link = $crewMember->getLink()) {
            $crewMemberFrontend->setLink($link);

            if($crewMember->getLinkLabel() || $crewMember->getLinkLabelBg()) {
                $linkLabel = $locale === $this->secondaryLocale ? $crewMember->getLinkLabelBg() : $crewMember->getLinkLabel();
                $crewMemberFrontend->setLinkLabel($linkLabel);
            }

        }

        $crewMemberFrontend->setPictureUrl($crewMember->getPictureUrl());

        if($secondPicture = $crewMember->getSecondPictureUrl()) {
            $crewMemberFrontend->setSecondPictureUrl($secondPicture);
        }

        return $crewMemberFrontend;
    }
}