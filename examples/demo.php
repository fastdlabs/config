<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/30
 * Time: 下午10:22
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

$composer = include __DIR__ . '/../vendor/autoload.php';

use Dobee\Configuration\Configuration;
use Dobee\Configuration\Loader\YamlFileLoader;
use Dobee\Configuration\Loader\IniFileLoader;
use Dobee\Configuration\Loader\PhpFileLoader;

$config = Configuration::createConfigurationLoader();

$config->addLoader(new YamlFileLoader(__DIR__ . '/config.yml'));
//$config->addLoader(new YamlFileLoader(__DIR__ . '/config2.yml'));
//$config->addLoader(new IniFileLoader(__DIR__ . '/config.ini'));
//$config->addLoader(new PhpFileLoader(__DIR__ . '/config.php'));

$options = $config->getOptions();

print_r($options);

