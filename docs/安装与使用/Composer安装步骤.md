# Composer 安装步骤

## 环境准备

在开始安装之前，请确保您已经安装了 Composer。如果尚未安装，请按照以下步骤进行：

### 安装 Composer

```bash
# 下载 Composer 安装脚本
curl -sS https://getcomposer.org/installer | php

# 移动到全局位置（可选）
mv composer.phar /usr/local/bin/composer
```

或者使用包管理器安装：

```bash
# Ubuntu/Debian
sudo apt install composer

# CentOS/RHEL/Fedora
sudo dnf install composer

# macOS
brew install composer

# Windows (使用 Chocolatey)
choco install composer
```

### 验证安装

```bash
composer --version
```

确保输出 Composer 版本信息。

## 项目初始化

如果您的项目还没有 `composer.json` 文件，需要先初始化项目：

```bash
composer init
```

按照提示填写项目信息，或者直接创建一个基础的 `composer.json` 文件。

## 安装 FastD\Config

### 方法一：命令行安装（推荐）

在您的项目根目录中运行以下命令：

```bash
composer require fastd/config
```

这将自动下载并安装 FastD\Config 及其依赖项，并更新 `composer.json` 和 `composer.lock` 文件。

### 方法二：手动编辑 composer.json

如果您希望手动控制依赖，可以在 `composer.json` 文件中添加依赖：

```json
{
    "require": {
        "fastd/config": "^3.0"
    }
}
```

然后运行：

```bash
composer install
```

### 方法三：指定版本安装

如果您需要安装特定版本：

```bash
# 安装最新稳定版本
composer require fastd/config:^3.0

# 安装特定版本
composer require fastd/config:3.0.0

# 安装预发布版本（如果有）
composer require fastd/config:dev-master
```

## 安装验证

### 检查安装状态

```bash
composer show fastd/config
```

这将显示已安装的 FastD\Config 信息，包括版本号和描述。

### 查看依赖树

```bash
composer show --tree
```

查看项目的完整依赖关系树。

## 自动加载配置

### 确保自动加载

在您的 PHP 代码中引入 Composer 的自动加载器：

```php
<?php
require_once 'vendor/autoload.php';

use FastD\Config\FileParser;

// 现在可以使用 FastD\Config 了
$parser = new FileParser();
```

### PSR-4 自动加载

FastD\Config 使用 PSR-4 自动加载标准，命名空间映射如下：

```
FastD\Config\ => vendor/fastd/config/src/
```

## 开发依赖安装

如果您还需要安装开发依赖（如单元测试工具）：

```bash
# 安装所有依赖（包括开发依赖）
composer install --dev

# 或者只安装开发依赖
composer install --only=dev
```

## 生产环境优化

### 优化自动加载

在生产环境中，建议优化自动加载以提高性能：

```bash
composer dump-autoload --optimize --classmap-authoritative
```

### 安装生产依赖

在生产环境中只安装生产依赖：

```bash
composer install --no-dev --optimize-autoloader
```

## 常见安装问题及解决方案

### 问题1: 权限错误

**现象**: 出现权限拒绝错误
**解决方案**: 
```bash
# 确保当前用户对 vendor 目录有写权限
chmod -R 755 vendor/
# 或者更改所有者
chown -R $USER:$USER vendor/
```

### 问题2: 网络连接问题

**现象**: 下载依赖时超时或连接失败
**解决方案**:
```bash
# 更换镜像源（以阿里云为例）
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 清除缓存重试
composer clear-cache
composer install
```

### 问题3: 版本冲突

**现象**: 出现依赖版本冲突
**解决方案**:
```bash
# 查看冲突详情
composer install -vvv

# 尝试更新依赖
composer update

# 或强制安装
composer require fastd/config --ignore-platform-reqs
```

### 问题4: 内存限制

**现象**: 安装过程中出现内存不足错误
**解决方案**:
```bash
# 增加内存限制
php -d memory_limit=-1 $(which composer) require fastd/config
```

## 更新和卸载

### 更新到最新版本

```bash
composer update fastd/config
```

### 查看可用版本

```bash
composer show --all fastd/config
```

### 卸载 FastD\Config

```bash
composer remove fastd/config
```

## Composer 配置优化

### 项目级配置

在 `composer.json` 中添加配置以优化性能：

```json
{
    "config": {
        "process-timeout": 0,
        "use-include-path": false,
        "preferred-install": "dist",
        "sort-packages": true,
        "optimize-autoloader": true,
        "classmap-authoritative": true
    }
}
```

### 全局配置

```bash
# 设置全局配置
composer config -g process-timeout 3000
composer config -g use-github-api false
```

## 验证安装结果

创建一个简单的测试文件来验证安装：

```php
<?php
// test_installation.php
require_once 'vendor/autoload.php';

use FastD\Config\FileParser;

try {
    $parser = new FileParser();
    echo "FastD\Config 安装成功！\n";
    echo "FileParser 类可用\n";
    
    // 测试基本功能
    $config = [
        'test' => [
            'key' => 'value'
        ]
    ];
    
    $parsed = new \FastD\Config\Parsed($config);
    $result = $parsed->get('test.key');
    
    if ($result === 'value') {
        echo "基本功能测试通过！\n";
    } else {
        echo "基本功能测试失败\n";
    }
} catch (Exception $e) {
    echo "安装或配置存在问题: " . $e->getMessage() . "\n";
}
```

运行测试：

```bash
php test_installation.php
```

如果看到"安装成功"的消息，说明 FastD\Config 已正确安装并可以使用。