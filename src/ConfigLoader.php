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

    public static function loadEnv()
    {

    }

    /**
     * @param null $resource
     * @return array|mixed
     */
    public static function load($resource = null)
    {
        $extension = pathinfo($resource, PATHINFO_EXTENSION);

        if ('cache' == $extension) {
            $basename = pathinfo($resource, PATHINFO_BASENAME);
            $info = explode('.', $basename);
            $extension = array_pop($info);
            $extension .= '.' . array_pop($info);
            unset($info, $basename);
        }

        switch ($extension) {
            case "ini":
            case 'cache.ini':
                $config = static::loadIni($resource);
                break;
            case "yml":
            case 'cache.yml':
                $config = static::loadYml($resource);
                break;
            case 'php':
            case 'cache.php':
            default:
                $config = static::loadPhp($resource);
        }

        return $config;
    }
}