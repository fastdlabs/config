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
    const DEFAULT_CACHE_DIR = __DIR__;
    const DEFAULT_CACHE_NAME = '.user';
    const DEFAULT_CACHE_SUFFIX = '.cache';

    const CACHE_PHP = '.php';
    const CACHE_YML = '.yml';
    const CACHE_INI = '.ini'; // cannot support.

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
     * @param string $name
     */
    public function __construct(Config $config, $dir = ConfigCache::DEFAULT_CACHE_DIR, $name = ConfigCache::DEFAULT_CACHE_NAME)
    {
        $this->config = $config;

        $this->dir = $dir;

        $this->name = $name;

        $this->cache = $dir . DIRECTORY_SEPARATOR . $name;
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
        return $this->name;
    }

    /**
     * @param string $cacheType
     * @return string
     */
    public function getCacheFile($cacheType = ConfigCache::CACHE_PHP)
    {
        return $this->cache . $cacheType . static::DEFAULT_CACHE_SUFFIX;
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