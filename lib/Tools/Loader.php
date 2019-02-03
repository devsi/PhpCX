<?php
namespace PhpCX\Tools;

use Symfony\Component\Yaml\Yaml;

class Loader
{
    const configpath = __DIR__ . '/../../config/';

    /**
     *
     */
    protected function __construct() {
    }

    /**
     * Parse a yaml file.
     *
     * @param string $file
     */
    public static function yaml($file)
    {
        return Yaml::parseFile(static::configpath . $file . '.yml');
    }
}