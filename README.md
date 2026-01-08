# FastD\Config - 轻量级 PHP 配置解析器

[![Build Status](https://travis-ci.org/fastdlabs/config.svg?branch=master)](https://travis-ci.org/fastdlabs/config)
[![Latest Stable Version](https://poser.pugx.org/fastd/config/v/stable)](https://packagist.org/packages/fastd/config) 
[![Total Downloads](https://poser.pugx.org/fastd/config/downloads)](https://packagist.org/packages/fastd/config) 
[![Latest Unstable Version](https://poser.pugx.org/fastd/config/v/unstable)](https://packagist.org/packages/fastd/config) 
[![License](https://poser.pugx.org/fastd/config/license)](https://packagist.org/packages/fastd/config)

FastD\Config 是一个轻量级的 PHP 配置解析器，支持多种配置文件格式（JSON、YAML、INI、PHP），并提供强大的变量替换功能。它允许您使用统一的 API 来处理不同格式的配置文件，简化了配置管理的复杂性。

## 环境依赖说明

### PHP 版本要求

* **最低版本**: PHP 8.2
* **推荐版本**: PHP 8.2 或更高版本

### 依赖库

* **symfony/yaml**: ^8.0 (用于解析 YAML 格式配置文件)
* **ext-json**: * (PHP 内置扩展，用于解析 JSON 格式配置文件)

## 基础使用说明

### 安装

使用 Composer 进行安装：

```
composer require fastd/config
```

### 基本用法

```php
<?php
require_once 'vendor/autoload.php';

use FastD\Config\FileParser;

// 创建解析器实例
$parser = new FileParser();

// 解析配置文件
$config = $parser->parse('config/app.json');

// 访问配置值
echo $config->get('database.host');
```

### 变量替换

FastD\Config 支持变量替换功能，变量使用 `%` 符号标记：

```php
// 配置文件中使用变量
// database:
//   host: "%db_host%"
//   port: "%db_port%"

$variables = [
    'db_host' => 'localhost',
    'db_port' => 3306
];

$parser = new FileParser($variables);
$config = $parser->parse('config/database.yml');

// 变量会被替换
echo $config->get('database.host'); // localhost
```

### 支持的配置格式

* **JSON**: 标准 JSON 格式配置文件
* **YAML**: 人类可读的 YAML 格式配置文件
* **INI**: 传统的 INI 格式配置文件
* **PHP**: PHP 数组返回格式的配置文件

## 文档详细引导

详细文档请参考项目文档库，包含以下内容：

### 1. 项目概述
- [项目简介和核心功能](.qoder/repowiki/zh/content/项目概述/项目简介和核心功能.md)
- [技术栈和架构设计](.qoder/repowiki/zh/content/项目概述/技术栈和架构设计.md)
- [主要组件介绍](.qoder/repowiki/zh/content/项目概述/主要组件介绍.md)
- [变量替换机制说明](.qoder/repowiki/zh/content/项目概述/变量替换机制说明.md)

### 2. API 参考
- [FileParser 类 API 文档](.qoder/repowiki/zh/content/API参考/FileParser类API文档.md)
- [Parsed 类 API 文档](.qoder/repowiki/zh/content/API参考/Parsed类API文档.md)

### 3. 安装与使用
- [环境要求和依赖说明](.qoder/repowiki/zh/content/安装与使用/环境要求和依赖说明.md)
- [Composer 安装步骤](.qoder/repowiki/zh/content/安装与使用/Composer安装步骤.md)
- [基础使用方法和配置](.qoder/repowiki/zh/content/安装与使用/基础使用方法和配置.md)
- [不同格式配置文件处理示例](.qoder/repowiki/zh/content/安装与使用/不同格式配置文件处理示例.md)
- [变量替换使用案例](.qoder/repowiki/zh/content/安装与使用/变量替换使用案例.md)
- [单元测试说明和最佳实践](.qoder/repowiki/zh/content/安装与使用/单元测试说明和最佳实践.md)

## 测试

```
phpunit
```

## 贡献

欢迎任何形式的贡献！您可以通过以下方式参与项目：

- 🐛 [报告问题](https://github.com/JanHuang/container/issues)
- 💡 提交功能建议
- 🔧 贡献代码和文档
- ⭐ Star项目支持

请确保在提交Pull Request前：

1. 编写相应的测试用例
2. 确保所有测试通过
3. 遵循项目的代码风格
4. 更新相关文档

## License

MIT
