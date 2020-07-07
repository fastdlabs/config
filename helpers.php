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

/**
 * @param $value
 * @param array $variables
 *
 * @return string
 */
function replace(string $value, array $variables = []): string
{
    if ('env' === substr($value, 0, 3)) {
        $env = substr($value, 4);
        return getenv($env);
    }

    if (false === strpos($value, Config::GLUE)) {
        return $value;
    }

    return preg_replace_callback(sprintf('/%s(\w*\.*\w*)%s/', Config::GLUE, Config::GLUE), function ($match) use ($variables) {
        print_r($match);
        return $variables[$match[1]];
    }, $value);
}
