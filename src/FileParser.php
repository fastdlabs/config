<?php

declare(strict_types=1);

namespace FastD\Config;

use ArrayObject;
use Exception;
use Symfony\Component\Yaml\Yaml;

class FileParser
{
    public Parsed $var;

    public function __construct(array|string $vars = [], public Parsed $parsed = new Parsed())
    {
        $this->var = new Parsed(is_string($vars) ? $this->load($vars) : $vars);
    }

    public function load(string $file): array
    {
        return match (pathinfo($file, PATHINFO_EXTENSION)) {
            'ini'   => parse_ini_file($file, true),
            'yml'   => Yaml::parseFile($file),
            'json'  => json_decode(file_get_contents($file), true),
            'php'   => include $file,
            default => throw new Exception('Unsupported file type: '.$file),
        };
    }

    public function parse(string $file): Parsed
    {
        $parsed = $this->replace($this->load($file));

        return $this->parsed->merge([pathinfo($file, PATHINFO_FILENAME) => $parsed]);
    }

    protected function replace(mixed $data): mixed
    {
        return match (true) {
            is_array($data) => array_map(
                fn ($value) => $this->replace($value),
                $data
            ),
            is_string($data) => preg_replace_callback(
                '/%([a-zA-Z0-9._]+)%/',
                fn($matches) => $this->var->get($matches[1], $matches[0]),
                $data
            ),
            default => $data,
        };
    }
}