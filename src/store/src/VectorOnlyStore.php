<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Store;

use Symfony\AI\Store\Document\VectorDocument;
use Symfony\AI\Store\Query\HybridQuery;
use Symfony\AI\Store\Query\QueryInterface;

/**
 * Wraps a store and disables HybridQuery support, forcing the Retriever to
 * use a pure VectorQuery. This is necessary for non-English corpora where
 * the hybrid query's full-text WHERE clause (hardcoded to 'english') would
 * filter out all results.
 *
 * @author Benjamin Zaslavsky <benjamin.zaslavsky@sensiolabs.com>
 */
final class VectorOnlyStore implements StoreInterface
{
    public function __construct(private readonly StoreInterface $inner) {}

    public function add(VectorDocument|array $documents): void
    {
        $this->inner->add($documents);
    }

    public function supports(string $queryClass): bool
    {
        if (HybridQuery::class === $queryClass) {
            return false;
        }

        return $this->inner->supports($queryClass);
    }

    public function query(QueryInterface $query, array $options = []): iterable
    {
        return $this->inner->query($query, $options);
    }
}
