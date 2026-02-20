<?php

declare(strict_types=1);

namespace Knebekaise\SortedLinkedList;

/**
 * Compares int|string values using PHP's natural ordering (spaceship operator).
 * Integers are compared numerically; strings are compared lexicographically.
 */
final class NaturalComparator implements Comparator
{
    public function compare(int|string $a, int|string $b): int
    {
        return $a <=> $b;
    }
}
