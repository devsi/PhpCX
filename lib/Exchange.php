<?php
namespace PhpCX;

use PhpCX\Tools\Loader;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;

class Exchange
{
    /**
     * Name of exchange
     * @var string
     */
    protected $name;

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
     * @param string $name Name of exchange.
     */
    public function __construct($name) {
        $this->name = $name;
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
        return sprintf('%s/', $this->definition['host']);
    }



    /**
     * Query the loaded api.
     *
     * @param string $endpoint
     */
    protected function query($endpoint, $params)
    {
        try {
            $http = new Guzzle(['base_uri' => $this->host()]);
            $response = $http->get($endpoint, ['query' => $params]);
        } catch( ClientException $e) {
            return $e->getMessage();
        }

        return (string) $response->getBody();
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