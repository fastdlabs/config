# Config

![Building](https://api.travis-ci.org/JanHuang/config.svg?branch=master)
[![Latest Stable Version](https://poser.pugx.org/fastd/config/v/stable)](https://packagist.org/packages/fastd/config) 
[![Total Downloads](https://poser.pugx.org/fastd/config/downloads)](https://packagist.org/packages/fastd/config) 
[![Latest Unstable Version](https://poser.pugx.org/fastd/config/v/unstable)](https://packagist.org/packages/fastd/config) 
[![License](https://poser.pugx.org/fastd/config/license)](https://packagist.org/packages/fastd/config)

简单的 PHP 配置解析器，依赖实现于: [Utils](https://github.com/JanHuang/utils)

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

## License MIT

