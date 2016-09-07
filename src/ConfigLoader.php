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
     * @return array
     */
    public static function loadPhp($resource)
    {
        return include $resource;
    }

    /**
     * @param $resource
     * @return array
     */
    public static function loadYml($resource)
    {
        return Yaml::parse(file_get_contents($resource));
    }

    /**
     * @param array $env
     * @return array
     */
    public static function loadEnv(array $env)
    {
        $config = [];

        foreach ($env as $item) {
            $config[$item] = getenv($item);
        }

        return $config;
    }

    /**
     * @param null $resource
     * @return array
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
                $config = static::loadIni($resource);
                break;
            case "yml":
                $config = static::loadYml($resource);
                break;
            case 'php':
            case 'cache':
            default:
                $config = static::loadPhp($resource);
        }

        return $config;
    }
}