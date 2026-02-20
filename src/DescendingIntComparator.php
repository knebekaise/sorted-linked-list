<?php

declare(strict_types=1);

namespace Knebekaise\SortedLinkedList;

use InvalidArgumentException;

/**
 * Compares integers in descending order.
 *
 * @throws InvalidArgumentException if either value is not an integer
 */
final class DescendingIntComparator implements Comparator
{
    public function compare(int|string $a, int|string $b): int
    {
        if (!is_int($a) || !is_int($b)) {
            throw new InvalidArgumentException('DescendingIntComparator only accepts integers.');
        }

        return $b <=> $a;
    }
}
