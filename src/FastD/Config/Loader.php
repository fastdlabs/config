<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 下午10:15
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Loader
 *
 * @package FastD\Configuration
 */
abstract class Loader
{
    /**
     * @param null $resource
     * @return mixed
     */
    abstract public function parse($resource = null);

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