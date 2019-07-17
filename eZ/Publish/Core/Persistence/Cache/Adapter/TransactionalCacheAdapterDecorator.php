<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Cache\Adapter;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Internal proxy adapter invalidating cache items on transaction commits/rollbacks.
 */
class TransactionalCacheAdapterDecorator implements TagAwareAdapterInterface
{
    /** @var \Symfony\Component\Cache\Adapter\TagAwareAdapterInterface */
    protected $innerPool;

    /** @var int */
    protected $transactionNestingLevel;

    /** @var array */
    protected $deferredTagsInvalidation;

    /** @var array */
    protected $deferredItemsDeletion;

    /**
     * @param \Symfony\Component\Cache\Adapter\TagAwareAdapterInterface $innerPool
     * @param int $transactionNestingLevel
     * @param array $deferredTagsInvalidation
     * @param array $deferredItemsDeletion
     */
    public function __construct(
        TagAwareAdapterInterface $innerPool,
        int $transactionNestingLevel = 0,
        array $deferredTagsInvalidation = [],
        array $deferredItemsDeletion = []
    ) {
        $this->innerPool = $innerPool;
        $this->transactionNestingLevel = $transactionNestingLevel;
        $this->deferredTagsInvalidation = $deferredTagsInvalidation;
        $this->deferredItemsDeletion = $deferredItemsDeletion;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        /** @var \Symfony\Component\Cache\CacheItem $newItem */
        $item = $this->innerPool->getItem($key);

        if ($this->transactionNestingLevel > 0) {
            $item->expiresAfter(0);
            $this->innerPool->save($item);
            return $this->innerPool->getItem($key);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        if ($this->transactionNestingLevel > 0) {
            // disable cache
        }

        return $this->innerPool->getItems($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        if ($this->transactionNestingLevel > 0) {
            // disable cache ?
        }

        return $this->innerPool->hasItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        if ($this->transactionNestingLevel > 0) {
            $this->deferredItemsDeletion[$this->transactionNestingLevel][] = $key;

            return true;
        }

        return $this->innerPool->deleteItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        if ($this->transactionNestingLevel > 0) {
            $this->deferredItemsDeletion[$this->transactionNestingLevel] += $keys;

            return true;
        }

        return $this->innerPool->deleteItems($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateTags(array $tags)
    {
        if ($this->transactionNestingLevel > 0) {
            $this->deferredTagsInvalidation[$this->transactionNestingLevel] += $tags;

            return true;
        }

        return $this->innerPool->invalidateTags($tags);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->deferredItemsDeletion = [];
        $this->deferredTagsInvalidation = [];
        --$this->transactionNestingLevel;

        return $this->innerPool->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        return $this->innerPool->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->innerPool->saveDeferred($item);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        return $this->innerPool->commit();
    }

    public function enableTransactionMode(): void
    {
        ++$this->transactionNestingLevel;
        $this->deferredTagsInvalidation[$this->transactionNestingLevel] = [];
        $this->deferredItemsDeletion[$this->transactionNestingLevel] = [];
    }

    public function disableTransactionMode(): void
    {
        $this->invalidateDeferredTags();
        $this->deleteDeferredItems();
        --$this->transactionNestingLevel;
    }

    protected function invalidateDeferredTags(): void
    {
        $tags = $this->deferredTagsInvalidation[$this->transactionNestingLevel];

        $this->innerPool->invalidateTags(array_unique($tags));
    }

    protected function deleteDeferredItems(): void
    {
        $keys = $this->deferredItemsDeletion[$this->transactionNestingLevel];

        $this->innerPool->deleteItems(array_unique($keys));
    }
}
