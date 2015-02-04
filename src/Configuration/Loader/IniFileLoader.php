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

namespace Dobee\Configuration;

use Dobee\Configuration\ConfigurationFileNotFoundException;

class IniFileLoader extends LoaderAbstract
{
    public function parser($resource = null)
    {
        if (!file_exists($resource)) {
            throw new ConfigurationFileNotFoundException(sprintf('%s\' is not found.', $resource));
        }

        return $this->setOptions(parse_ini_file($resource, true));
    }

    public function load($resource = null)
    {
        return $this->parser($resource);
    }
}