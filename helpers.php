<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
use Symfony\Component\Yaml\Yaml;

/**
 * @param $file
 *
 * @return array
 */
function load($file)
{
    $extension = pathinfo($file, PATHINFO_EXTENSION);

    switch ($extension) {
        case 'ini':
            $config = parse_ini_file($file, true);
            break;
        case 'yml':
            $config = Yaml::parse(file_get_contents($file));
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
