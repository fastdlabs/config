<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */


namespace FastD\Config;

use FastD\Config\Exceptions\ConfigCacheUnableException;
use RuntimeException;

/**
 * Class ConfigCache
 *
 * @package FastD\Config
 */
class ConfigCache
{
    const CACHE_NAME = '.config.cache';

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $dir;

    /**
     * ConfigCache constructor.
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = $this->targetCacheDirectory($dir);

        $this->file = $this->dir . DIRECTORY_SEPARATOR . static::CACHE_NAME;
    }

    /**
     * @param $dir
     * @return string
     */
    protected function targetCacheDirectory($dir)
    {
        if (!is_string($dir)) {
            throw new RuntimeException(sprintf('Cannot open cache directory "%s".', $dir));
        }

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        return rtrim($dir, '/');
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        if (!$this->hasCache()) {
            return false;
        }

        return is_writeable($this->file);
    }

    /**
     * @return bool
     */
    public function hasCache()
    {
        return file_exists($this->file);
    }

    /**
     * @return null|string
     */
    public function getCacheDir()
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getCacheFile()
    {
        return $this->file;
    }

    /**
     * @param $data
     * @return $this
     */
    public function saveCache(array $data)
    {
        if (empty($this->file)) {
            throw new ConfigCacheUnableException('En... Not setting cache file.');
        }

        file_put_contents($this->file, $this->dump($data));

        return $this;
    }
    /**
     * @return array
     */
    public function loadCache()
    {
        if (!$this->hasCache()) {
            throw new ConfigCacheUnableException($this->file);
        }

        return ConfigLoader::load($this->file);
    }

    /**
     * @param array $data
     * @return string
     */
    public function dump($data)
    {
        return '<?php return ' . var_export($data, true) . ';';
    }
}