<?php

namespace Craft;

use Exception;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Stream\PhpStreamRequestFactory;

/**
 * Tobias AX asset download service
 */
class TobiasAx_AssetDownloadService extends BaseApplicationComponent
{
    /**
     * @var integer
     */
    const DEFAULT_IMAGE_WIDTH = 800;

    /**
     * @var integer
     */
    const DEFAULT_IMAGE_HEIGHT = 600;

    /**
     * @var integer
     */
    const DEFAULT_MAX_SIZE = 10;

    /**
     * Download and resize assets for given types
     * @param TobiasAx_PublicationAssetsModel $assetsModel
     * @param array $types
     * @param bool $resizeImages
     * @return array containing file paths
     */
    public function downloadAssets($assetsModel, $types = null, $resizeImages = true)
    {
        $directory = craft()->path->getTempUploadsPath();

        if ($assetsModel instanceof ITobiasAx_AssetUrlResource) {
            $urls = $this->formatAssetUrls($assetsModel, $types);
            $files = $this->downloadFiles($urls, $directory);
        } else {
            $assets = $assetsModel->getByType($types);
            $files = $this->downloadStreams($assets, $directory);
        }

        if ($resizeImages) {
            $images = $this->filterImages($files);
            $this->resizeImages($images);
        }

        return $files;
    }

    /**
     * Format asset URL's
     * @param TobiasAx_PublicationAssetsModel $assetsModel
     * @param array $types
     * @return array containg asset url's
     */
    public function formatAssetUrls($assetsModel, $types)
    {
        $urls = [];

        foreach ($assetsModel->getByType($types) as $asset) {
            $urls[] = $this->getAssetUrl($assetsModel->getAssetPath($asset));
        }

        return $urls;
    }

    /**
     * Download multiple files using webservice stream
     * @param TobiasAx_AssetModel[] $assets
     * @param  string $targetFolder
     * @return array
     */
    public function downloadStreams($assets, $targetFolder)
    {
        $files = [];

        foreach ($assets as $asset) {
            try {
                $saveTo = $targetFolder.$asset->Filename;
                $this->downloadStream($asset, $saveTo);
                $files[] = $saveTo;
            } catch (Exception $e) {
                TobiasAxPlugin::log($e->getMessage(), LogLevel::Error);
            }
        }

        return $files;
    }

    /**
     * Download a file from stream
     * @param TobiasAx_AssetModel $asset
     * @param string $saveTo
     * @throws Exception
     * @return bool
     */
    public function downloadStream($asset, $saveTo)
    {
        $request = craft()->tobiasAx_assetConnector
            ->getAssetResource($asset->Id, $asset->Checksum);
        $stream = (new PhpStreamRequestFactory())->fromRequest($request);
        $startFragment = '<GetDocumentFileResult>';
        $endFragment = '<';
        $start = $end = false;
        $maxBytes = 1024 * 1024 * $this->getMaxAssetSize();
        $bufferSize = 256;
        $content = '';

        // find file stream start position
        while ($start == false && !$stream->feof()) {
            $content .= $stream->read($bufferSize);
            $start = stripos($content, $startFragment);
        }

        // no start position found
        if ($start == false) {
            throw new Exception('File stream contents not found');
        }

        // prepend previous suffix
        $file = fopen($saveTo, 'w');
        $content = substr($content, $start + strlen($startFragment));
        fwrite($file, base64_decode($content));

        // stream content to temp file
        $size = 0;
        while ($end == false && !$stream->feof()) {
            if (($size += $bufferSize) > $maxBytes) {
                throw new Exception('File stream exceeds maximum size');
            }

            $content = $stream->read($bufferSize);
            $end = stripos($content, $endFragment);
            $content = substr($content, 0, $end | strlen($content));
            fwrite($file, base64_decode($content));
        }

        fclose($file);

        if ($end == false) {
            throw new Exception('Unkown error occured downloading file stream');
        }

        return file_exists($saveTo);
    }

    /**
     * Download multiple files using URL
     * @param  array $urls
     * @param  string $targetFolder
     * @return array
     */
    public function downloadFiles($urls, $targetFolder)
    {
        $files = [];

        foreach ($urls as $url) {
            try {
                $saveTo = $targetFolder.basename($url);
                $this->downloadFile($url, $saveTo);
                $files[] = $saveTo;
            } catch (Exception $e) {
                TobiasAxPlugin::log($e->getMessage(), LogLevel::Error);
            }
        }

        return $files;
    }

    /**
     * Download a file
     * @param string $url
     * @param string $saveTo
     * @throws Exception
     * @return bool
     */
    public function downloadFile($url, $saveTo)
    {
        $client = new \Guzzle\Http\Client();

        // download and save file
        $request = $client->get($url, [], [
            'save_to' => $saveTo
        ]);

        // use HTTP basic authentication
        $request->setAuth($this->getAssetUsername(), $this->getAssetPassword());

        // start downloading
        try {
            $response = $request->send();
        } catch (Exception $e) {
            throw new TobiasAx_SystemException(Craft::t('Error downloading file “{url}. Message: {message}”.', array('url' => $url, 'message' => $e->getMessage())), null, $e);
        }

        return $response->isSuccessful();
    }

    /**
     * Resizes images to configured size
     * @param string $file
     * @param bool $scaleIfSmaller
     * @return array containing resized images
     */
    public function resizeImages($files, $scaleIfSmaller = false)
    {
        $resized = [];

        foreach ($files as $file) {
            try {
                $this->resizeImage($file, $scaleIfSmaller);
                $resized[] = $file;
            } catch (Exception $e) {
                TobiasAxPlugin::log($e->getMessage(), LogLevel::Error);
            }
        }

        return $resized;
    }

    /**
     * Resize image to configured size
     * @param string $file
     * @param bool $scaleIfSmaller
     * @return void
     * @throws TobiasAx_SystemException
     */
    public function resizeImage($file, $scaleIfSmaller = false)
    {
        try {
            $image = craft()->images->loadImage($file);
            $image->scaleToFit($this->getAssetWidth(), $this->getAssetHeight(), $scaleIfSmaller);
            $image->saveAs($file);
        } catch (Exception $e) {
            throw new TobiasAx_SystemException(Craft::t('Error resizing image “{file}. Message: {message}”.', array('file' => $file, 'message' => $e->getMessage())), null, $e);
        }
    }

    /**
     * Filters images based on file extension
     * @param string[] $files
     * @return string[]
     */
    public function filterImages($files)
    {
        $service = $this;
        $images = array_filter($files, function ($file) use ($service) {
            return $service->isAllowedImageExtension(IOHelper::getExtension($file));
        }, ARRAY_FILTER_USE_BOTH);

        return $images;
    }

    /**
     * Whether given image extension is allowed
     * @param string $extension
     * @return boolean
     */
    public function isAllowedImageExtension($extension)
    {
        return in_array($extension, $this->getAllowedImageExtensions());
    }

    /**
     * @param string $path
     * @return string
     */
    public function getAssetUrl($path)
    {
        $endpoint = craft()->config->get('tobiasAxAssetEndpoint');

        return $endpoint.$path;
    }

    /**
     * @return string
     */
    protected function getAssetUsername()
    {
        return craft()->config->get('tobiasAxUsername');
    }

    /**
     * @return string
     */
    protected function getAssetPassword()
    {
        return craft()->config->get('tobiasAxPassword');
    }

    /**
     * @return int
     */
    protected function getAssetWidth()
    {
        return craft()->config->get('tobiasAxAssetWidth') ?? static::DEFAULT_IMAGE_WIDTH;
    }

    /**
     * @return int
     */
    protected function getAssetHeight()
    {
        return craft()->config->get('tobiasAxAssetHeight') ?? static::DEFAULT_IMAGE_HEIGHT;
    }

    /**
     * @return array
     */
    protected function getAllowedImageExtensions()
    {
        return craft()->config->get('tobiasAxAllowedImageExtensions') ?? ['jpg', 'jpeg'];
    }

    /**
     * @return array
     */
    protected function getMaxAssetSize()
    {
        return craft()->config->get('tobiasAxAssetMaxSize') ?? static::DEFAULT_MAX_SIZE;
    }
}
