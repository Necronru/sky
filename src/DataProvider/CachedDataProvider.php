<?php
namespace DataProvider;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CachedDataProvider implements DataProviderInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheExpiresVia;

    /**
     * @param DataProviderInterface  $decoratedProvider
     * @param CacheItemPoolInterface $cache
     * @param string                 $cacheExpiresVia
     */
    public function __construct(DataProviderInterface $decoratedProvider,
                                CacheItemPoolInterface $cache,
                                $cacheExpiresVia = '+1 day')
    {
        $this->logger = new NullLogger();
        $this->cache = $cache;
        $this->dataProvider = $decoratedProvider;
        $this->cacheExpiresVia = $cacheExpiresVia;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function get(array $parameters)
    {
        try {
            $cacheKey = $this->getCacheKey($parameters);
            $cacheItem = $this->cache->getItem($cacheKey);

            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->dataProvider->get($parameters);

            $cacheItem
                ->set($result)
                ->expiresAt((new \DateTime())->modify($this->cacheExpiresVia))
            ;

            return $result;

        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            throw $e;
        }
    }

    public function getCacheKey($value)
    {
        return md5(serialize($value));
    }

    /**
     * @param string $cacheExpiresVia
     */
    public function setCacheExpiresVia($cacheExpiresVia)
    {
        $this->cacheExpiresVia = $cacheExpiresVia;
    }
}