<?php
declare(strict_types=1);

namespace FastD\Config;

use ArrayObject;
use Exception;
use Symfony\Component\Yaml\Yaml;


class FileParser
{
    public const PARSE_RETURN = 0;
    public const PARSE_APPEND = 1;
    private const GLUE = '%';

    public function __construct(protected array $vars = [], protected Parsed $parseResult = new Parsed())
    {
    }

    public function parse(string $file, int $flag = FileParser::PARSE_APPEND): Parsed
    {
        $config = match (pathinfo($file, PATHINFO_EXTENSION)) {
            'ini' => parse_ini_file($file, true),
            'yml' => Yaml::parseFile($file),
            'json' => json_decode(file_get_contents($file), true),
            'php' => include $file,
            default => throw new Exception('Unsupported file type: '.$file),
        };

        $config = $this->replace($config, $this->vars);

        if ($flag === FileParser::PARSE_APPEND) {
            $this->parseResult->exchangeArray($config);
            return $this->parseResult;
        }

        return new Parsed($config);
    }

    private function replace(mixed $data, array $variables): mixed {
        return match (true) {
            is_array($data) => array_map(
                fn ($value) => $this->replace($value, $variables),
                $data
            ),
            is_string($data) => preg_replace_callback(
                '/%([^%]+)%/',
                fn (array $matches) =>
                (array_key_exists($matches[1], $variables) && $variables[$matches[1]] !== null)
                    ? $variables[$matches[1]]
                    : $matches[0],
                $data
            ),
            default => $data,
        };
    }
}