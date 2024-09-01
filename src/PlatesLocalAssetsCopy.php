<?php
declare(strict_types=1);

/**
 * This file `PlatesLocalAssetsCopy.php` is part of the package basteyy/plates-local-assets-copy.
 * @website https://github.com/basteyy/plates-local-assets-copy
 * @author basteyy <sebastian@xzit.online>
 * @license The Unlicense
 * @see https://github.com/basteyy/plates-local-assets-copy/blob/master/LICENSE
 */

namespace basteyy\PlatesLocalAssetsCopy;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Template;

/**
 * Class PlatesLocalAssetsCopy
 * @package basteyy\PlatesLocalAssetsCopy
 */
class PlatesLocalAssetsCopy implements ExtensionInterface
{
    /** @var Template $template In case dynamic properties are not allowed, to avoid the exception */
    public Template $template;
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
    public function getLocalCached(string $url): string
    {
        $filename = basename($url);
        $cacheFile = $this->cachePath . '/' . $filename;
        $filemtime = file_exists($cacheFile) ? filemtime($cacheFile) : false;

        if (!$filemtime || $filemtime < $this->cacheDeadline) {
            $this->downloader->download($url);

            if ($this->downloader->getStatusCode() !== 200) {
                throw new Exception('Unable to download remote file');
            }

            $filemtime = time();
        }

        return $this->publicPath . '/' . $filename . '?timestamp=' . date('d-m-y_h-i-s', $filemtime);
    }
}
