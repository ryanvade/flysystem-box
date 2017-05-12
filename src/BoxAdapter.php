<?php

namespace FlysystemBox;

use LogicException;
use League\Flysystem\Config;
use LaravelBox\LaravelBox as Client;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;

class BoxAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    protected $client;

    public function __construct(Client $client, string $prefix = '')
    {
        $this->client = $client;

        $this->setPathPrefix($prefix);
    }

    public function write($path, $contents, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadContents($contents, $path);

        return $resp; // TODO return $resp->toArray();
    }

    public function writeStream($path, $resource, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadStreamContents($resource, $path);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadContentsVersion($contents, $path);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadStreamContentsVersion($contents, $path);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newPath)
    {
        $path = $this->applyPathPrefix($path);
        $newPath = $this->applyPathPrefix($newPath);
        $resp = $this->client->moveFile($path, $newPath);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        $path = $this->applyPathPrefix($path);
        $newPath = $this->applyPathPrefix($newPath);
        $resp = $this->client->copyFile($path, $newPath);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->deleteFile($path);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname): bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileInformation($path);

        return $resp->getCode() < 400;
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileStreamDownload($path);

        return $resp; // TODO What is different compated to readStream?
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileStreamDownload($path);

        return $resp; // TODO What is different compated to readStream?
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false): array
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileInformation($path);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileInformation($path);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        throw new LogicException("Box Api does not support MimeTypes for path ${path}");
    }

    public function getTemporaryLink(string $path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileEmbeddedLink($path);

        return $resp; // TODO return $resp->toArray();
    }

    public function getThumbnail(string $path, string $format = 'png', string $size = 'w64h64')
    {
        $path = $this->applyPathPrefix($path);
        // TODO: Return stream resource
        // TODO: Size parameter...
        $resp = $this->client->fileThumbnailStream($path, $extension);

        return $resp; // TODO return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function applyPathPrefix($path): string
    {
        $path = parent::applyPathPrefix($path);

        return '/'.trim($path, '/');
    }
}
