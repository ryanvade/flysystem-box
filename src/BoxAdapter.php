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

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadContents($contents, $path);

        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadStreamContents($resource, $path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadContentsVersion($contents, $path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        // TODO Preflight Check
        $resp = $this->client->uploadStreamContentsVersion($contents, $path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newPath)
    {
        $path = $this->applyPathPrefix($path);
        $newPath = $this->applyPathPrefix($newPath);
        $resp = $this->client->moveFile($path, $newPath);
        if ($resp->isError()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        $path = $this->applyPathPrefix($path);
        $newPath = $this->applyPathPrefix($newPath);
        $resp = $this->client->copyFile($path, $newPath);
        if ($resp->isError()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->deleteFile($path);

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        $path = $this->applyPathPrefix($dirname);
        $resp = $this->client->deleteFolder($path, true); // Going to assume recursive
        if ($resp->isError()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        $path = $this->applyPathPrefix($dirname);
        $resp = $this->client->createFolder($path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileInformation($path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileStreamDownload($path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray(); // TODO What is different compated to readStream?
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileStreamDownload($path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray(); // TODO What is different compated to readStream?
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false): array
    {
        $path = $this->applyPathPrefix($path);
        $count = $this->client->getFolderItemsCount($path);
        if ($count < 0) {
            return false;
        }
        $offset = 0;
        $limit = 1000;
        $items = array();
        do {
            $resp = $this->client->getFolderItems($path, $offset, $limit);
            if ($resp->isError()) {
                return false;
            }
            array_push($items, $resp->getJson()->entries);
            $offset = $offset + $limit;
            $limit = ($count - $offset < 1000) ? $count - $offset : 1000;
        } while ($offset != $count && $count > 0);

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileInformation($path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileInformation($path);
        if ($resp->isError()) {
            return false;
        }

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        throw new LogicException("Box Api does not support MimeTypes for path ${path}");
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryLink(string $path)
    {
        $path = $this->applyPathPrefix($path);
        $resp = $this->client->fileEmbeddedLink($path);

        return $resp->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnail(string $path, string $format = 'png', string $size = 'w64h64')
    {
        $path = $this->applyPathPrefix($path);
        // TODO: Return stream resource
        // TODO: Size parameter...
        $resp = $this->client->fileThumbnailStream($path, $extension);

        return $resp->toArray();
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
