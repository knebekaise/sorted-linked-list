<?php

declare(strict_types=1);

namespace Knebekaise\SortedLinkedList;

use InvalidArgumentException;

/**
 * Compares strings case-insensitively using strcasecmp.
 *
 * @throws InvalidArgumentException if either value is not a string
 */
final class CaseInsensitiveStringComparator implements Comparator
{
    public function compare(int|string $a, int|string $b): int
    {
        if (!is_string($a) || !is_string($b)) {
            throw new InvalidArgumentException('CaseInsensitiveStringComparator only accepts strings.');
        }

        return strcasecmp($a, $b);
    }
}
