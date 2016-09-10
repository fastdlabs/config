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

use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigCache
 *
 * @package FastD\Config
 */
class ConfigCache
{
    const DEFAULT_CACHE_SUFFIX = '.cache';
    const CACHE_NAME = '.config';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $cache;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var string
     */
    protected $name;

    /**
     * ConfigCache constructor.
     *
     * @param Config $config
     * @param string $dir
     */
    public function __construct(Config $config, $dir = __DIR__)
    {
        $this->config = $config;

        $this->dir = $dir;

        $this->cache = $dir . DIRECTORY_SEPARATOR . static::CACHE_NAME . static::DEFAULT_CACHE_SUFFIX;
    }

    /**
     * @return null|string
     */
    public function getCacheDir()
    {
        return $this->dir;
    }

    /**
     * @return null|string
     */
    public function getCacheName()
    {
        return static::CACHE_NAME;
    }

    /**
     * @return string
     */
    public function getCacheFile()
    {
        return $this->cache;
    }

    /**
     * @param string $cacheType
     * @return mixed
     */
    public function getCacheFileName($cacheType = ConfigCache::CACHE_PHP)
    {
        return pathinfo($this->getCacheFile($cacheType), PATHINFO_BASENAME);
    }

    /**
     * @param string $cacheType
     * @return $this
     */
    public function saveCacheFile($cacheType = ConfigCache::CACHE_PHP)
    {
        if (!is_dir(dirname($this->cache))) {
            mkdir(dirname($this->cache), 0755, true);
        }

        $cache = $this->cache . $cacheType . static::DEFAULT_CACHE_SUFFIX;

        file_put_contents($cache, $this->dump($cacheType));

        return $this;
    }

    /**
     * @param string $cacheType
     * @return array|mixed
     */
    public function loadCache($cacheType = ConfigCache::CACHE_PHP)
    {
         return ConfigLoader::load($this->getCacheFile($cacheType));
    }

    /**
     * @param string $cacheType
     * @return string
     */
    public function dump($cacheType = ConfigCache::CACHE_PHP)
    {
        switch ($cacheType) {
            case ConfigCache::CACHE_YML:
                $content = Yaml::dump($this->config->all(), 4);
                break;
            case ConfigCache::CACHE_INI:
                throw new \InvalidArgumentException('Cannot support ini cache type. Please you try "yml" or "php" save it.');
                break;
            case ConfigCache::CACHE_PHP:
            default:
                $content = '<?php return ' . var_export($this->config->all(), true) . ';';
        }

        return $content;
    }
}