<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;
    /**
     * @var string
     */
    private $languageSettingParamName;
    /**
     * @var string
     */
    private $defaultLocale;
    /**
     * @var string
     */
    private $secondaryLocale;

    public function __construct(
        string $locale,
        string $languageSettingParamName,
        string $defaultLocale,
        string $secondaryLocale
    ) {
        $this->locale = $locale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->defaultLocale = $defaultLocale;
        $this->secondaryLocale = $secondaryLocale;
    }

    public function getGlobals()
    {
        return [
            'locale' => $this->locale,
            'language_setting_param' => $this->languageSettingParamName,
            'default_locale' => $this->defaultLocale,
            'secondary_locale' => $this->secondaryLocale
        ];
    }

}