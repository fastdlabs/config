<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Config\Config;
use Symfony\Component\Yaml\Yaml;

/**
 * @param string $file
 *
 * @return array
 */
function load(string $file): array
{
    switch (pathinfo($file, PATHINFO_EXTENSION)) {
        case 'ini':
            $config = parse_ini_file($file, true);
            break;
        case 'yml':
            $config = Yaml::parseFile($file);
            break;
        case 'json':
            $config = json_decode(file_get_contents($file), true);
            break;
        case 'php':
        default:
            $config = include $file;
    }

    return $config;
}
