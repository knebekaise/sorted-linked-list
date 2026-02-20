<?php

declare(strict_types=1);

namespace Knebekaise\SortedLinkedList;

/**
 * @internal
 * @template T of int|string
 */
final class Node
{
    /** @var Node<T>|null */
    public ?Node $next = null;

    /**
     * @param T $value
     */
    public function __construct(
        public readonly int|string $value,
    ) {}
}
