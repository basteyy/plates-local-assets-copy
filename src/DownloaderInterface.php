<?php

declare(strict_types=1);

namespace basteyy\PlatesLocalAssetsCopy;

interface DownloaderInterface
{
    /**
     * Setup the local path
     * @param string $localPath
     */
    public function setLocalPath(string $localPath) : void;

    /**
     * Process the download of file $url
     * @param string $url
     */
    public function download(string $url) : void;

    /**
     * Returns the status code
     * @return false|int
     */
    public function getStatusCode(): bool|int;
}