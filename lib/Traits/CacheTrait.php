<?php
namespace PhpCX\Traits;

use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

Trait CacheTrait
{
    /**
     * Whether to disable cache.
     * @var bool
     */
    protected $cacheDisabled;

    /**
     * How long to cache the response for
     * @var int
     */
    protected $cacheTtl = 60;

    /**
     * Caches an api response.
     *
     * @param string $key
     * @param string $content [optional]
     */
    protected function cache($key, $content = null)
    {
        // return if cache is disabled
        if ($this->cacheDisabled) {
            return $content;
        }

        $cache = $this->cache->getItem($key);
        $cache->expiresAfter($this->cacheTtl);

        if ($content) {
            $cache->set($content);
            $this->cache->save($cache);
        }

        return $cache->get();
    }

    /**
     * Sets a cache interface, defaults to PHP Files Cache.
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache = null)
    {
        if (!$cache) {
            $cache = new PhpFilesAdapter('phpcx', $this->cacheTtl);
        }

        return $cache;
    }

    /**
     * Disable content caching
     */
    public function disableCache() {
        $this->cacheDisabled = true;

        return $this;
    }

    /**
     * Enable content caching
     */
    public function enableCache() {
        $this->cacheDisabled = false;

        return $this;
    }

    /**
     * Set ttl of cache files.
     *
     * @param int $ttl
     */
    public function setCacheTtl($ttl) {
        $this->cacheTtl = $ttl;

        return $this;
    }
}