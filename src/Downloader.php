<?php

declare(strict_types=1);

namespace basteyy\PlatesLocalAssetsCopy;


class Downloader implements DownloaderInterface
{
    /**
     * @var string Local path for the downloaded file
     */
    private string $localPath;
    /**
     * @var mixed
     */
    private $statusCode = null;

    /**
     * Setup the local path
     * @param string $localPath
     * @throws \Exception
     */
    public function setLocalPath(string $localPath): void
    {
        $localPath = rtrim($localPath, '/');

        if (!is_dir($localPath)) {
            throw new \Exception(sprintf('Folder "%s" not exists.', $localPath));
        }

        $this->localPath = $localPath;
    }

    /**
     * Process the download of file $url
     * @param string $url
     * @throws \Exception
     */
    public function download(string $url): void
    {
        if (!isset($this->localPath)) {
            throw new \Exception('No local path set');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception(sprintf('The given url "%s" seems to be invalid.', $url));
        }

        $fp = fopen($this->localPath . '/' . basename($url), 'w');

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
