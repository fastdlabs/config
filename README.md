# Config

[![Build Status](https://travis-ci.org/fastdlabs/config.svg?branch=master)](https://travis-ci.org/fastdlabs/config)
[![Latest Stable Version](https://poser.pugx.org/fastd/config/v/stable)](https://packagist.org/packages/fastd/config) 
[![Total Downloads](https://poser.pugx.org/fastd/config/downloads)](https://packagist.org/packages/fastd/config) 
[![Latest Unstable Version](https://poser.pugx.org/fastd/config/v/unstable)](https://packagist.org/packages/fastd/config) 
[![License](https://poser.pugx.org/fastd/config/license)](https://packagist.org/packages/fastd/config)

简单的 PHP 配置解析器，依赖实现于: [Utils](https://github.com/fastdlabs/utils)

## Requirements

* PHP >= 5.6

## Composer

```
composer require fastd/config
```

## Usage

```php
use FastD\Config\Config;

$config = new Config(array $config, array $variables = []);

$config->load($file);

$config->find($key, $default);
```

变量使用 `%` 来标记. 如: `%name%`

## Testing

```
phpunit
```

### Support

如果你在使用中遇到问题，请联系: [bboyjanhuang@gmail.com](mailto:bboyjanhuang@gmail.com). 微博: [编码侠](http://weibo.com/ecbboyjan)

### 贡献

非常欢迎感兴趣，愿意参与其中，共同打造更好PHP生态，Swoole生态的开发者。

如果你乐于此，却又不知如何开始，可以试试下面这些事情：

* 在你的系统中使用，将遇到的问题 [反馈](https://github.com/JanHuang/fastD/issues)。
* 有更好的建议？欢迎联系 [bboyjanhuang@gmail.com](mailto:bboyjanhuang@gmail.com) 或 [新浪微博:编码侠](http://weibo.com/ecbboyjan)。

### 联系

如果你在使用中遇到问题，请联系: [bboyjanhuang@gmail.com](mailto:bboyjanhuang@gmail.com). 微博: [编码侠](http://weibo.com/ecbboyjan)

## License MIT
