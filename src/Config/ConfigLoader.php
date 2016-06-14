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
     * @param null $resource
     * @return array|mixed
     */
    public static function load($resource = null)
    {
        switch (pathinfo($resource, PATHINFO_EXTENSION)) {
            case "ini":
                $config = parse_ini_file($resource, true);
                break;
            case "yml":
            case "yaml":
                $config = Yaml::parse(file_get_contents($resource));
                break;
            case 'php':
            default:
                $config = include $resource;;
        }

        return $config;
    }
}