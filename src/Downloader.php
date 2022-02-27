<?php

declare(strict_types=1);

namespace basteyy\PlatesLocalAssetsCopy;


class Downloader implements DownloaderInterface
{
    /**
     * @var mixed
     */
    private $statusCode = null;

    /**
     * Process the download of file $url
     * @param string $url
     * @param string $target_location Where the file should be downloaded to
     * @throws \Exception
     */
    public function download(string $url, string $target_location): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception(sprintf('The given url "%s" seems to be invalid.', $url));
        }

        $fp = fopen($target_location, 'w');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($ch);
        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        fclose($fp);
    }

    /**
     * Returns the status code
     * @return false|int
     */
    public function getStatusCode() : false|int
    {
        return $this->statusCode ?? false;
    }
}
