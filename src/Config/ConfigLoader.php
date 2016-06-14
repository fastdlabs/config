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
 * Class ConfigLoader
 *
 * @package FastD\Config
 */
class ConfigLoader
{
    /**
     * @param $resource
     * @return array
     */
    public static function loadIni($resource)
    {
        return parse_ini_file($resource, true);
    }

    /**
     * @param $resource
     * @return mixed
     */
    public static function loadPhp($resource)
    {
        return include $resource;
    }

    /**
     * @param $resource
     * @return mixed
     */
    public static function loadYml($resource)
    {
        return Yaml::parse(file_get_contents($resource));
    }

    /**
     * @param null $resource
     * @return array|mixed
     */
    public static function load($resource = null)
    {
        switch (pathinfo($resource, PATHINFO_EXTENSION)) {
            case "ini":
                $config = static::loadIni($resource);
                break;
            case "yml":
                $config = static::loadYml($resource);
                break;
            case 'php':
            default:
                $config = static::loadPhp($resource);
        }

        return $config;
    }
}