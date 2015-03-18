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
echo '<pre>';
$composer = include __DIR__ . '/../vendor/autoload.php';

use Dobee\Configuration\Loader\YmlFileLoader;
use Dobee\Configuration\Loader\PhpFileLoader;
use Dobee\Configuration\Loader\IniFileLoader;

$config = new \Dobee\Configuration\Config();

if ($config->getCaching()) {
    print_r($config);
    die;
}

$config->load(__DIR__ . '/config.yml');

print_r($config);
$config->setCaching();
