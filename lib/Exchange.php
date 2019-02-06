<?php
namespace PhpCX;

use PhpCX\Tools\Loader;
use PhpCX\Traits\CacheTrait;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;

class Exchange
{
    use CacheTrait;

    /**
     * Name of config.
     * @var string
     */
    protected $name;

    /**
     * Content cache dependency.
     * @var CacheInterface
     */
    protected $cache;

    /**
     * Yaml config data.
     * @var array
     */
    protected $definition;

    /**
     * Map of methods.
     * @var array
     */
    protected $methods;

    /**
     * Maps private and public categories to methods.
     * @var array
     */
    protected $maps;

    /**
     * API constants.
     */
    const apiprivate = 1;
    const apipublic = 2;
    const apiboth = 3;

    /**
     * @param string $name Name of config file.
     * @param CacheInterface $cache [optional]
     */
    public function __construct($name, CacheInterface $cache = null) {
        $this->name = $name;
        $this->cache = $this->setCache($cache);

        $this->definition = (Loader::yaml($name))[$name];
        $this->methods = $this->define(static::apiboth);
    }

    /**
     * Define api.
     *
     * @param int $type
     */
    private function define($type)
    {
        switch ($type) {
            case static::apiprivate:
                $endpoints = 'private';
            case static::apipublic;
                $endpoints = 'public';
            default:
                $endpoints = null;
        }

        $all = $this->definition['api'];

        // map public and private calls
        $this->maps = [];
        foreach ($all as $access => &$methods) {
            $map = array_fill_keys(array_keys($methods), $access);
            $this->maps = array_merge($this->maps, $map);
        }

        return array_key_exists($endpoints, $all) ? $all[$endpoints] : $all;
    }

    /**
     * Return the host url.
     */
    private function host()
    {
        if (!empty($this->definition['version'])) {
            return sprintf('%s/%s/', $this->definition['host'], $this->definition['version']);
        }

        return sprintf('%s/', $this->definition['host']);
    }

    /**
     * Query the loaded api.
     *
     * @param string $endpoint
     */
    protected function query($endpoint, $params)
    {
        $params = $this->params($params);
        $cachekey = str_replace('/', '.', "$this->name.$endpoint");

        // return cached if it exists.
        $content = $this->cache($cachekey);
        if ($content) {
            return $content;
        }

        // create a path string from params array
        $path = implode('/', $params['path']);
        if ($path) {
            $path = "/$path";
        }

        try {
            $http = new Guzzle(['base_uri' => $this->host()]);
            $response = $http->get($endpoint.$path, ['query' => $params['query']]);
        } catch( ClientException $e) {
            return $e->getMessage();
        }

        $content = (string) $response->getBody();
        $this->cache($cachekey, $content);

        return $content;
    }

    /**
     * Breaks down incoming params into an organised array of slugs/query.
     */
    protected function params($params)
    {
        $organised = ['path' => [], 'query' => []];
        foreach ($params as $key => $param) {
            if (\mb_substr($key, 0, 1) === '#') {
                $newkey = \mb_substr($key, 1, strlen($key));
                $organised['path'][$newkey] = $param;
                unset($key);
            } else {
                $organised['query'][$key] = $param;
            }
        }

        array_multisort($organised);

        return $organised;
    }

    /**
     * Magic __call is how we execute our dynamic methods from yaml config.
     *
     * @param string $name
     * @param array $args
     */
    public function __call($name, $args)
    {
        if (array_key_exists($name, $this->maps)) {
            return $this->resolve($name, $args);
        }
    }

    /**
     * Resolve a hidden method.
     *
     * @param string $name
     * @param array $args
     */
    private function resolve($name, $args)
    {
        $access = $this->maps[$name];
        $def = $this->methods[$access][$name];

        $endpoint = $def[0];
        $params = array_slice($def, 1);

        //$args = array_pad($args, sizeof($params), null);
        $params = array_intersect_key($params, $args);
        $args = array_intersect_key($args, $params);
        $params = array_combine($params, $args);

        return $this->query($endpoint, $params);
    }
}
