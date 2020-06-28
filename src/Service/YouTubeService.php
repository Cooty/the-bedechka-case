<?php

namespace App\Service;

use App\Model\Transport\Video;
use App\Model\Transport\VideoThumbnails;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_PlaylistItem;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_ThumbnailDetails;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;


class YouTubeService
{
    const CLIENT_NAME = 'TheBedechkaCaseWebsite';
    const CLIENT_SCOPES = ['https://www.googleapis.com/auth/youtube.readonly'];
    const LANG_SPLIT = ' / ';
    const DESCRIPTION_LOCALIZE_BORDER = '.....';
    const DEFAULT_THUMBNAIL_MAP = ['mobile'=> 'high', 'tablet'=> 'standard', 'desktop'=> 'maxres'];
    const YT_API_MAX_RESULTS_PER_PAGE = 50;

    /**
     * @var string
     */
    private $secondaryLocale;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Google_Client
     */
    private $client;

    /**
     * @var Google_Service_YouTube
     */
    private $service;

    public function __construct(
        string $secondaryLocale,
        LoggerInterface $logger,
        string $clientSecretFile
    )
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName(self::CLIENT_NAME);
        $this->client->setScopes(self::CLIENT_SCOPES);

        try {
            $this->client->setAuthConfig($clientSecretFile);
        } catch (\Google_Exception $e) {
            $this->logger->error($e->getMessage().' '.$e->getTraceAsString());
        }

        $this->client->setAccessType('offline');

        $headers = array('Referer' => getenv('HOST_NAME'));
        $guzzleClient = new Client([
            'curl' => [CURLOPT_SSL_VERIFYPEER => false],
            'headers' => $headers
        ]);
        $this->client->setHttpClient($guzzleClient);
        $this->client->setDeveloperKey(getenv('YT_API_KEY'));

        $this->service = new Google_Service_YouTube($this->client);
        $this->secondaryLocale = $secondaryLocale;
        $this->logger = $logger;
    }

    /**
     * @param array $thumbnailsMap
     * @param Google_Service_YouTube_ThumbnailDetails $ytThumbnails
     * @return VideoThumbnails
     */
    private function makeThumbnails(
        array $thumbnailsMap,
        Google_Service_YouTube_ThumbnailDetails $ytThumbnails
    ): VideoThumbnails
    {
        $thumbnails = new VideoThumbnails();

        $url = null;
        foreach($thumbnailsMap as $key => $value) {
            $url = $ytThumbnails[$value] ? $ytThumbnails[$value]->url : $ytThumbnails['high']->url;

            switch ($key) {
                case 'mobile':
                    $thumbnails->setMobile($url);
                    break;
                case 'tablet':
                    $thumbnails->setTablet($url);
                    break;
                case 'desktop':
                    $thumbnails->setDesktop($url);
                    break;
            }
        }

        return $thumbnails;
    }

    /**
     * @param Google_Service_YouTube_Video|Google_Service_YouTube_PlaylistItem
     * @param array $thumbnailsMap
     * @return Video
     */
    private function makeFrontendVideo(
        $item,
        array $thumbnailsMap
    ): Video
    {
        $video = new Video();

        /**
         * @var Google_Service_YouTube_VideoSnippet
         */
        $snippet = $item->snippet;

        if($item instanceof Google_Service_YouTube_Video) {
            $video->setId($item->id);
        } else {
            $video->setId($item->snippet->resourceId->videoId);
        }

        list($titleEn, $titleBg) = $this->splitTextByLang($snippet->title);

        $video->setTitle($titleEn);
        $video->setTitleBg($titleBg);

        $video->setThumbnails($this->makeThumbnails($thumbnailsMap, $snippet->thumbnails));

        list ($descriptionEn, $descriptionBg) = $this->getDescriptionLocalizedParts($snippet->description);
        $video->setDescription($descriptionEn);
        $video->setDescriptionBg($descriptionBg);

        return $video;
    }

    /**
     * The YT videos have titles like "Parvan Simeonov / Първан Симеонов"
     * we need to only display the part before the " / " on the English version of the site
     * and the part after " / " on the Bulgarian
     * This maybe replaced in the future with youtube localized meta data, but it's
     * good enough for now
     * @see https://support.google.com/youtube/answer/6300772?hl=en
     *
     * @param string $text
     * @return string[] - The 0th item is English, the 1st is Bulgarian
     */
    private function splitTextByLang(string $text): array
    {
        if(strpos($text, self::LANG_SPLIT) === false) {
            return [trim($text), trim($text)];
        }

        $index = strpos($text, self::LANG_SPLIT);

        $en = mb_substr($text, 0, $index + 1);
        $bg = mb_substr($text, $index + strlen(self::LANG_SPLIT));

        return [$en, $bg];
    }

    /**
     * We use the "description" field of the YT videos to add who are the protagonists
     * under "people" page.
     * This also has to be translated and we use the " / " to localize it
     * @see docs for YouTubeService::splitTextByLang
     * We also reserve the rest of the description to not be translated and displayed here
     * only on YT. So if there's "....." string in the description that part won't
     * go into the translated / displayed string
     *
     * @param $description
     * @return string[] - The 0th item is English, the 1st is Bulgarian
     */
    private function getDescriptionLocalizedParts(string $description): array
    {
        if(strpos($description, self::DESCRIPTION_LOCALIZE_BORDER) === false) {
            return $this->splitTextByLang($description);
        }

        $index = strpos($description, self::DESCRIPTION_LOCALIZE_BORDER);
        $descriptionTranslatedPart = mb_substr($description, 0, $index + 1);

        return $this->splitTextByLang($descriptionTranslatedPart);
    }

    /**
     * @param string $id
     * @param array $thumbnailsMap
     * @return Video|null
     */
    public function getSingleVideo(
        string $id,
        array $thumbnailsMap = self::DEFAULT_THUMBNAIL_MAP
    ): ?Video
    {
        try {
            $queryParams = [
                'id'=> $id,
                'fields'=> urlencode('items(id,snippet(title,thumbnails,description))')
            ];

            $response = $this->service->videos->listVideos(
                'snippet',
                $queryParams);

            return $this->makeFrontendVideo($response->getItems()[0], $thumbnailsMap);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage().' '.$e->getTraceAsString());
            return null;
        }

    }

    public function getVideosFromPlaylist(
        string $id,
        array $thumbnailsMap = self::DEFAULT_THUMBNAIL_MAP
    ): array
    {
        try {
            $queryParams = [
                'maxResults' => self::YT_API_MAX_RESULTS_PER_PAGE,
                'playlistId' => $id,
                'fields'=> urlencode('items(snippet(title,thumbnails,description,resourceId(videoId)))')
            ];

            $response = $this->service->playlistItems->listPlaylistItems('snippet', $queryParams);
            $items = $response->getItems();

            $videos = array_map(function($item) use($thumbnailsMap) {
                return $this->makeFrontendVideo($item, $thumbnailsMap);
            }, (array)$items);

            return $videos;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage().' '.$e->getTraceAsString());
            return [];
        }
    }
}