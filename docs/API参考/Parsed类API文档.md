# Parsed 类 API 文档

`Parsed` 类继承自 PHP 的 `ArrayObject`，提供了便捷的配置访问功能，支持点号分隔的嵌套键访问和多种配置操作方法。

## 类定义

```php
class Parsed extends ArrayObject
{
    public function merge(array $parsed): self;
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function has(string $key): bool;
    public function unset(string $key): void;
    
    // ArrayAccess 接口方法
    public function offsetGet($key): mixed;
    public function offsetSet($key, $value): void;
    public function offsetExists($key): bool;
    public function offsetUnset($key): void;
}
```

## 继承关系

- **父类**: `ArrayObject`
- **实现接口**: `ArrayAccess`, `IteratorAggregate`, `Traversable`, `ArrayAccess`, `Serializable`, `Countable`

## 公共方法

### `merge(array $parsed): self`

将传入的数组与当前配置合并，支持深度合并嵌套数组。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $parsed | array | 要合并的数组数据 |

#### 返回值

- **类型**: `self` (Parsed)
- **说明**: 返回当前对象实例，支持链式调用

#### 合并规则

- 如果原值和新值都是数组，则递归合并
- 如果原值不是数组但新值是数组，则将原值转换为数组再合并
- 如果键为字符串，则直接赋值
- 如果键为数字，则追加到数组末尾

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed([
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ],
    'cache' => [
        'driver' => 'file'
    ]
]);

// 合并新配置
$parsed->merge([
    'database' => [
        'username' => 'root',
        'password' => 'password'
    ],
    'logging' => [
        'level' => 'info'
    ]
]);

// 结果:
// [
//     'database' => [
//         'host' => 'localhost',
//         'port' => 3306,
//         'username' => 'root',
//         'password' => 'password'
//     ],
//     'cache' => [
//         'driver' => 'file'
//     ],
//     'logging' => [
//         'level' => 'info'
//     ]
// ]

// 链式调用
$parsed->merge(['a' => 1])->merge(['b' => 2])->merge(['c' => 3]);
```

### `get(string $key, mixed $default = null): mixed`

获取配置值，支持点号分隔的嵌套键访问。

#### 参数

| 参数名 | 类型 | 默认值 | 说明 |
|--------|------|--------|------|
| $key | string | - | 配置键，支持点号分隔的嵌套路径 |
| $default | mixed | `null` | 当键不存在时返回的默认值 |

#### 返回值

- **类型**: `mixed`
- **说明**: 配置值或默认值

#### 访问规则

1. 优先检查直接键是否存在（如 `database`）
2. 如果不存在且键包含点号，则按路径访问（如 `database.host`）
3. 逐级验证路径中每个键是否存在
4. 返回最终路径的值或默认值

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed([
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'credentials' => [
            'username' => 'root',
            'password' => 'password'
        ]
    ],
    'app' => 'MyApp'
]);

// 直接访问
echo $parsed->get('app'); // 'MyApp'

// 嵌套访问
echo $parsed->get('database.host'); // 'localhost'
echo $parsed->get('database.credentials.username'); // 'root'

// 带默认值访问
echo $parsed->get('database.timeout', 30); // 30 (不存在的键，返回默认值)
echo $parsed->get('database.port', 5432); // 3306 (存在的键，返回实际值)

// 特殊键名处理
$parsed2 = new Parsed([
    'database.host' => 'direct_value',
    'database' => ['host' => 'nested_value']
]);

echo $parsed2->get('database.host'); // 'direct_value' (优先返回直接键)
```

### `set(string $key, mixed $value): void`

设置配置值，支持点号分隔的嵌套键设置。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $key | string | 配置键，支持点号分隔的嵌套路径 |
| $value | mixed | 要设置的值 |

#### 说明

- 如果键存在直接匹配，则直接设置该键的值
- 如果键包含点号，则按路径设置嵌套值
- 自动创建必要的嵌套结构
- 支持设置任意类型的数据

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed();

// 设置简单值
$parsed->set('app_name', 'MyApp');
echo $parsed->get('app_name'); // 'MyApp'

// 设置嵌套值
$parsed->set('database.host', 'localhost');
$parsed->set('database.port', 3306);
$parsed->set('database.credentials.username', 'root');
$parsed->set('database.credentials.password', 'password');

// 结果结构:
// [
//     'app_name' => 'MyApp',
//     'database' => [
//         'host' => 'localhost',
//         'port' => 3306,
//         'credentials' => [
//             'username' => 'root',
//             'password' => 'password'
//         ]
//     ]
// ]

// 更新已存在的嵌套值
$parsed->set('database.host', '192.168.1.100');
echo $parsed->get('database.host'); // '192.168.1.100'

// 设置复杂数据类型
$parsed->set('middlewares', ['auth', 'cors', 'logger']);
$parsed->set('database.options', [
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci'
]);
```

### `has(string $key): bool`

检查配置键是否存在，支持点号分隔的嵌套键检查。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $key | string | 配置键，支持点号分隔的嵌套路径 |

#### 返回值

- **类型**: `bool`
- **说明**: 键是否存在

#### 检查规则

1. 优先检查直接键是否存在
2. 如果不存在且键包含点号，则按路径检查
3. 逐级验证路径中每个键是否存在
4. 只有路径完全存在才返回 true

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed([
    'database' => [
        'host' => 'localhost',
        'credentials' => [
            'username' => 'root'
        ]
    ],
    'app.name' => 'direct_key'
]);

var_dump($parsed->has('database')); // true
var_dump($parsed->has('database.host')); // true
var_dump($parsed->has('database.port')); // false
var_dump($parsed->has('database.credentials.username')); // true
var_dump($parsed->has('database.credentials.password')); // false
var_dump($parsed->has('app.name')); // true (直接键存在)
var_dump($parsed->has('app')); // false (没有名为 'app' 的直接键)
```

### `unset(string $key): void`

删除配置键，支持点号分隔的嵌套键删除。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $key | string | 配置键，支持点号分隔的嵌套路径 |

#### 说明

- 如果键不包含点号，则直接删除该键
- 如果键包含点号，则按路径删除嵌套值
- 保持其他配置数据不变

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed([
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'credentials' => [
            'username' => 'root',
            'password' => 'password'
        ]
    ],
    'cache' => 'redis'
]);

// 删除简单键
$parsed->unset('cache');
var_dump($parsed->has('cache')); // false

// 删除嵌套键
$parsed->unset('database.credentials.password');
// 结果中 database.credentials 只包含 ['username' => 'root']

// 删除顶层嵌套键
$parsed->unset('database.port');
// 结果中 database 只包含 host 和 credentials

var_dump($parsed->has('database.credentials.password')); // false
```

## ArrayAccess 接口方法

### `offsetGet($key): mixed`

实现 ArrayAccess 接口，通过数组语法访问配置值。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $key | mixed | 配置键 |

#### 返回值

- **类型**: `mixed`
- **说明**: 配置值或 null

#### 说明

- 内部调用 `get($key, null)` 方法
- 支持点号分隔的嵌套键访问

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed([
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ]
]);

// 数组式访问
echo $parsed['database.host']; // 'localhost'
echo $parsed['database.port']; // 3306
```

### `offsetSet($key, $value): void`

实现 ArrayAccess 接口，通过数组语法设置配置值。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $key | mixed | 配置键 |
| $value | mixed | 要设置的值 |

#### 说明

- 内部调用 `set($key, $value)` 方法
- 支持点号分隔的嵌套键设置

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed();

// 数组式设置
$parsed['database.host'] = 'localhost';
$parsed['database.port'] = 3306;

echo $parsed->get('database.host'); // 'localhost'
```

### `offsetExists($key): bool`

实现 ArrayAccess 接口，通过数组语法检查配置键是否存在。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $key | mixed | 配置键 |

#### 返回值

- **类型**: `bool`
- **说明**: 键是否存在

#### 说明

- 内部调用 `has($key)` 方法
- 支持点号分隔的嵌套键检查

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed([
    'database.host' => 'localhost'
]);

var_dump(isset($parsed['database.host'])); // true
var_dump(isset($parsed['database.port'])); // false
```

### `offsetUnset($key): void`

实现 ArrayAccess 接口，通过数组语法删除配置键。

#### 参数

| 参数名 | 类型 | 说明 |
|--------|------|------|
| $key | mixed | 配置键 |

#### 说明

- 内部调用 `unset($key)` 方法
- 支持点号分隔的嵌套键删除

#### 示例

```php
use FastD\Config\Parsed;

$parsed = new Parsed([
    'database.host' => 'localhost',
    'database.port' => 3306
]);

// 数组式删除
unset($parsed['database.port']);

var_dump($parsed->has('database.port')); // false
```

## 使用示例

### 基础操作

```php
use FastD\Config\Parsed;

// 创建配置对象
$config = new Parsed([
    'app' => [
        'name' => 'MyApp',
        'version' => '1.0.0'
    ],
    'debug' => true
]);

// 获取值
echo $config->get('app.name'); // 'MyApp'
echo $config['debug']; // true

// 设置值
$config->set('app.author', 'Developer');
$config['app.license'] = 'MIT';

// 检查键是否存在
if ($config->has('app.name')) {
    echo "App name: " . $config->get('app.name');
}

// 删除键
$config->unset('debug');
unset($config['app.version']);
```

### 配置合并

```php
use FastD\Config\Parsed;

$baseConfig = new Parsed([
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ],
    'cache' => [
        'driver' => 'file'
    ]
]);

$envConfig = [
    'database' => [
        'username' => 'root',
        'password' => 'password'
    ],
    'logging' => [
        'level' => 'info'
    ]
];

$baseConfig->merge($envConfig);

// 结果包含所有配置
echo $baseConfig->get('database.username'); // 'root'
echo $baseConfig->get('logging.level'); // 'info'
```

### 遍历配置

由于继承自 ArrayObject，Parsed 对象可以被遍历：

```php
use FastD\Config\Parsed;

$config = new Parsed([
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ],
    'app' => 'MyApp'
]);

// 遍历配置
foreach ($config as $key => $value) {
    echo "$key: " . json_encode($value) . "\n";
}
```

## 注意事项

1. **键名冲突**: 当同时存在直接键和嵌套路径时，直接键优先级更高
2. **类型安全**: 方法参数和返回值都经过类型检查
3. **性能考虑**: 嵌套访问需要解析路径，对于深层嵌套可能影响性能
4. **数组操作**: 由于继承自 ArrayObject，支持所有 ArrayObject 的操作
5. **线程安全**: Parsed 类不是线程安全的，在多线程环境中需要额外处理