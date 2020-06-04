<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use App\Enum\Pagination;
use Twig\TwigFunction;

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

    /**
     * @var string
     */
    private $mapBoxToken;

    /**
     * @var string
     */
    private $publicDirectory;

    public function __construct(
        string $locale,
        string $languageSettingParamName,
        string $defaultLocale,
        string $secondaryLocale,
        string $mapBoxToken,
        string $publicDirectory
    ) {
        $this->locale = $locale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->defaultLocale = $defaultLocale;
        $this->secondaryLocale = $secondaryLocale;
        $this->mapBoxToken = $mapBoxToken;
        $this->publicDirectory = $publicDirectory;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('is_facebook_link', [$this, 'isFacebookLink']),
            new TwigFunction('get_asset_content', [$this, 'getAssetContent'])
        ];
    }

    public function isFacebookLink(string $url): bool
    {
        return strpos($url, 'facebook.com') !== false;
    }

    public function getAssetContent($asset): string
    {
        try {
            $cacheKey = md5($asset);
            dump($cacheKey);

            $content = file_get_contents($this->publicDirectory.$asset);

            return $content;
        } catch(\Exception $e) {
            return '';
        }
    }

    public function getGlobals()
    {
        return [
            'locale' => $this->locale,
            'language_setting_param' => $this->languageSettingParamName,
            'default_locale' => $this->defaultLocale,
            'secondary_locale' => $this->secondaryLocale,
            'map_box_token' => $this->mapBoxToken,
            'news_items_per_page' => Pagination::NEWS_PAGE_SIZE
        ];
    }

}