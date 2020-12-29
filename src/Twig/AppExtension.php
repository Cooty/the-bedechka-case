<?php

namespace App\Twig;

use Psr\Cache\InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use App\Enum\Pagination;
use Twig\TwigFunction;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
    private $publicDirectory;

    /**
     * @var CacheInterface
     */
    private $appCache;

    public function __construct(
        string $locale,
        string $languageSettingParamName,
        string $defaultLocale,
        string $secondaryLocale,
        string $publicDirectory,
        CacheInterface $appCache
    ) {
        $this->locale = $locale;
        $this->languageSettingParamName = $languageSettingParamName;
        $this->defaultLocale = $defaultLocale;
        $this->secondaryLocale = $secondaryLocale;
        $this->publicDirectory = $publicDirectory;
        $this->appCache = $appCache;
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

            $content = $this->appCache->get($cacheKey, function() use ($asset) {
                return file_get_contents($this->publicDirectory.$asset);
            });

            return $content;
        } catch(\Exception $e) {
            return '';
        } catch (InvalidArgumentException $e) {
            return '';
        }
    }

    public function getGlobals(): array
    {
        return [
            'locale' => $this->locale,
            'language_setting_param' => $this->languageSettingParamName,
            'default_locale' => $this->defaultLocale,
            'secondary_locale' => $this->secondaryLocale,
            'news_items_per_page' => Pagination::NEWS_PAGE_SIZE
        ];
    }

}