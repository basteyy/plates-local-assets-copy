<?php

declare(strict_types=1);

namespace basteyy\PlatesLocalAssetsCopy;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

/**
 * Class PlatesLocalAssetsCopy
 * @package basteyy\PlatesLocalAssetsCopy
 */
class PlatesLocalAssetsCopy implements ExtensionInterface
{
    private string $cachePath;
    private string $publicPath;
    private int $cacheDeadline;
    /**
     * @var Downloader
     */
    private Downloader $downloader;

    /**
     * PlatesLocalAssetsCopy constructor.
     * @param string $cachePath Local (private) cache path
     * @param string $publicPath Local public cache path (for example https://example.com/assets/css/)
     * @param int $cacheTimeout Timeout, when a cached file will be reloaded
     * @param DownloaderInterface|null $downloader Downloader
     * @throws Exception
     */
    #[NoReturn] public function __construct(string $cachePath, string $publicPath, int $cacheTimeout = 3600, DownloaderInterface $downloader = null)
    {
        if (!$downloader) {
            $this->downloader = new Downloader();
        }

        $cachePath = rtrim($cachePath, '/');
        $publicPath = rtrim($publicPath, '/');

        $this->cachePath = $cachePath;
        $this->publicPath = $publicPath;
        $this->cacheDeadline = (time() - $cacheTimeout);
    }

    /**
     * @param Engine $engine
     */
    public function register(Engine $engine): void
    {
        $engine->registerFunction('cacheLocal', [$this, 'getLocalCached']);
    }

    /**
     * @param string $url Url of the remote file
     * @return string
     * @throws Exception
     */
    public function getLocalCached(string $url, bool $AbsoluteUrl = true): string
    {

        $pathInformation = pathinfo(strtolower(basename($url))); // Cause of google fonts as an extra variable
        if(!isset($pathInformation['extension'])){
            // Google Scenario!
            if(substr($pathInformation['basename'], 0, 4) === 'css2') {
                $basename = 'css2_' . md5($url);
                $extension = 'google';
            } else {
                $extension = false;
                $basename = false;
            }
        } else {
            $basename = $pathInformation['basename'];
            $extension = $pathInformation['extension'];
        }

        if (in_array($extension, ['google', 'jpg', 'css', 'jpeg', 'js', 'png', 'gif'])) {

            $cachedFileStoragePath = $this->cachePath . '/' . $extension .'/';
            $cachedFilePublicStoragePath = $this->publicPath . '/' . $extension .'/';

            if (!is_dir($cachedFileStoragePath)) {
                mkdir($cachedFileStoragePath, 0766, true);
            }

            $cachedFilePath = $cachedFileStoragePath . $basename;
            $cachedFileInfoFilePath = $cachedFileStoragePath . $basename . '.json';

            $createCache = !file_exists($cachedFilePath);

            $currentDateTime = new \DateTime;

            if (!$createCache && file_exists($cachedFileInfoFilePath)) {
                $cachedFileMetaData = json_decode(file_get_contents($cachedFileInfoFilePath), true);

                if(isset($cachedFileMetaData['last_download'])){
                    $cachedDateTime = new \DateTime($cachedFileMetaData['last_download']);
                    if($currentDateTime > $cachedDateTime->modify('+24 hours')) {
                        $createCache = true;
                    }

                } else {
                    $createCache = true;
                }
            } else {
                $cachedFileMetaData = [];
            }

            if($createCache){
                    // Download the file
                    $this->downloader->download($url, $cachedFilePath);


                    if ($this->downloader->getStatusCode() !== 200) {
                        throw new Exception('Unable to download remote file');
                    }

                    $cachedFileMetaData['last_download'] = date('Y-m-d H:i:s');



                file_put_contents($cachedFileInfoFilePath, json_encode($cachedFileMetaData, JSON_PRETTY_PRINT));


            }

            return $AbsoluteUrl ? $this->AbsoluteUrl($cachedFilePublicStoragePath . $basename) : $cachedFilePublicStoragePath . $basename;
        }

        return $AbsoluteUrl ? $this->AbsoluteUrl($url) : $url; // No Cache allowed at this point

    }

    protected function AbsoluteUrl(string $url): string
    {
        if(substr($url, 0, 4) === 'http'){
            return $url;
        }
        return $this->getBaseUrl() . ltrim($url, '/');
    }

    protected function getBaseUrl(): string
    {
        return $this->protocol . $_SERVER['HTTP_HOST'] . '/';
    }
}