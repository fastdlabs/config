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
    protected $cache;

    /**
     * @var string
     */
    protected $dir;

    /**
     * ConfigCache constructor.
     *
     * @param string $dir
     */
    public function __construct($dir = null)
    {
        $this->dir = $this->targetCacheDirectory($dir);

        if (null !== $this->dir) {
            $this->cache = $this->dir . DIRECTORY_SEPARATOR . static::CACHE_NAME;
        }
    }

    /**
     * @param $dir
     * @return string
     */
    protected function targetCacheDirectory($dir)
    {
        if (empty($dir)) {
            return null;
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
            throw new ConfigCacheUnableException('null');
        }

        return is_writeable($this->cache);
    }

    /**
     * @return bool
     */
    public function hasCache()
    {
        return file_exists($this->cache);
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
        return $this->cache;
    }

    /**
     * @param $data
     * @return $this
     */
    public function saveCache(array $data)
    {
        if (empty($this->cache)) {
            throw new ConfigCacheUnableException('En... Not setting cache file.');
        }

        file_put_contents($this->cache, $this->dump($data));

        return $this;
    }
    /**
     * @return array
     */
    public function loadCache()
    {
        if (!$this->hasCache()) {
            throw new ConfigCacheUnableException($this->cache);
        }

        return ConfigLoader::load($this->cache);
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