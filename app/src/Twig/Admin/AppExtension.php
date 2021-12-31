<?php

namespace App\Twig\Admin;

use App\Twig\AppExtension as FrontendTwigExtensions;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends FrontendTwigExtensions
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('first_letter', [$this, 'getFirstLetter'])
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('random_bs_color_name', [$this, 'getRandomBsColorName'])
        ];
    }

    public function getFirstLetter($text): string
    {
        return (string)$text[0];
    }

    public function getRandomBsColorName(): string
    {
        $bsColors = [
            "primary",
            "secondary",
            "success",
            "info",
            "warning",
            "danger"
        ];

        return $bsColors[array_rand($bsColors)];
    }
}
