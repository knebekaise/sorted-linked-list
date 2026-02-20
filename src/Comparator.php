<?php

declare(strict_types=1);

namespace Knebekaise\SortedLinkedList;

interface Comparator
{
    /**
     * Compare two values.
     *
     * Returns a negative integer if $a < $b, zero if $a === $b, a positive integer if $a > $b.
     *
     * @param int|string $a
     * @param int|string $b
     */
    public function compare(int|string $a, int|string $b): int;
}
