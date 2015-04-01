<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/9
 * Time: 下午2:59
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Config\Caching;

/**
 * Class ConfigCaching
 *
 * @package Dobee\Configuration\Caching
 */
abstract class ConfigCaching
{
    /**
     * @var string
     */
    protected $cachePath = './';

    /**
     * @var string
     */
    protected $cacheName = 'config.php.cache';

    /**
     * @return string
     */
    public function getCachePath()
    {
        return realpath($this->cachePath);
    }

    /**
     * @param string $cachePath
     * @return $this
     */
    public function setCachePath($cachePath)
    {
        $this->cachePath = $cachePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheName()
    {
        return $this->cacheName;
    }

    /**
     * @param string $cacheName
     * @return $this
     */
    public function setCacheName($cacheName)
    {
        $this->cacheName = $cacheName;

        return $this;
    }

    abstract public function hasCaching();

    abstract public function setCaching();

    abstract public function getCaching();
}