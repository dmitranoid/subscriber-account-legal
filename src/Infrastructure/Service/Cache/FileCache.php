<?php
declare(strict_types=1);


namespace App\Infrastructure\Service\Cache;


use Webmozart\Assert\Assert;

class FileCache implements \Psr\SimpleCache\CacheInterface
{

    private string $path;

    public function __construct(string $path)
    {
        Assert::notEmpty($path);
        $this->path = rtrim($path, '\//');
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $filename = $this->generateFilename($key);
        if (!(file_exists($filename) & is_file($filename) & is_readable($filename))) {
            return $default;
        }
        return file_get_contents($filename);
    }

    private function generateFilename(string $key): string
    {
        Assert::notEmpty($key);
        return $this->path . DIRECTORY_SEPARATOR . $key;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $filename = $this->generateFilename($key);
        file_put_contents($filename, $value);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        $filename = $this->generateFilename($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        throw new \BadMethodCallException('Not implemented');
        // TODO: Implement getMultiple() method.
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        throw new \BadMethodCallException('Not implemented');

        // TODO: Implement setMultiple() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        throw new \BadMethodCallException('Not implemented');

        // TODO: Implement deleteMultiple() method.
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        $filename = $this->generateFilename($key);
        return ((file_exists($filename) & is_file($filename) & is_readable($filename)));
    }
}