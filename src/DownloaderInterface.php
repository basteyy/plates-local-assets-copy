<?php
declare(strict_types=1);

/**
 * This file `DownloaderInterface.php` is part of the package basteyy/plates-local-assets-copy.
 * @website https://github.com/basteyy/plates-local-assets-copy
 * @author basteyy <sebastian@xzit.online>
 * @license The Unlicense
 * @see https://github.com/basteyy/plates-local-assets-copy/blob/master/LICENSE
 */

namespace basteyy\PlatesLocalAssetsCopy;

interface DownloaderInterface
{
    /**
     * Process the download of file $url
     * @param string $url The url of the remote file
     * @param string $target_location The path (including filename) where the remote file should be stored
     */
    public function download(string $url, string $target_location) : void;

    /**
     * Returns the status code
     * @return false|int
     */
    public function getStatusCode(): bool|int;
}
