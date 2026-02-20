<?php

declare(strict_types=1);

namespace Knebekaise\SortedLinkedList;

use Countable;
use InvalidArgumentException;
use Iterator;

/**
 * A singly linked list that maintains elements in sorted (ascending) order.
 *
 * All elements must be of the same type: int or string.
 *
 * @template T of int|string
 * @implements Iterator<int, T>
 */
final class SortedLinkedList implements Countable, Iterator
{
    /** @var Node<T>|null */
    private ?Node $head = null;
    /** @var int<0, max> */
    private int $count = 0;

    // Iterator state
    /** @var Node<T>|null */
    private ?Node $current = null;
    private int $position = 0;

    public function __construct(
        private readonly Comparator $comparator = new NaturalComparator(),
    ) {
    }

    /**
     * Insert a value into the list, maintaining sorted order.
     *
     * @param T $value
     * @throws InvalidArgumentException if the value type is incompatible with existing elements
     */
    public function insert(int|string $value): void
    {
        $this->assertCompatibleType($value);

        $newNode = new Node($value);

        if ($this->head === null || $this->comparator->compare($value, $this->head->value) <= 0) {
            $newNode->next = $this->head;
            $this->head = $newNode;
            $this->count++;

            return;
        }

        $current = $this->head;
        while ($current->next !== null && $this->comparator->compare($current->next->value, $value) <= 0) {
            $current = $current->next;
        }

        $newNode->next = $current->next;
        $current->next = $newNode;
        $this->count++;
    }

    /**
     * Remove the first occurrence of a value from the list.
     *
     * @param T $value
     * @return bool true if the value was found and removed, false otherwise
     * @throws InvalidArgumentException if the value type is incompatible with existing elements
     */
    public function removeOne(int|string $value): bool
    {
        $this->assertCompatibleType($value);

        if ($this->head === null) {
            return false;
        }

        if ($this->comparator->compare($this->head->value, $value) === 0) {
            $this->head = $this->head->next;
            assert($this->count > 0);
            $this->count--;

            return true;
        }

        $current = $this->head;
        while ($current->next !== null) {
            if ($this->comparator->compare($current->next->value, $value) === 0) {
                $current->next = $current->next->next;
                assert($this->count > 0);
                $this->count--;

                return true;
            }
            if ($this->comparator->compare($current->next->value, $value) > 0) {
                break; // sorted: no match possible beyond this point
            }
            $current = $current->next;
        }

        return false;
    }

    /**
     * Remove all occurrences of a value from the list.
     *
     * @param T $value
     * @return int the number of nodes removed
     * @throws InvalidArgumentException if the value type is incompatible with existing elements
     */
    public function removeAll(int|string $value): int
    {
        $this->assertCompatibleType($value);

        $removed = 0;

        // Remove matching head nodes
        while ($this->head !== null && $this->comparator->compare($this->head->value, $value) === 0) {
            $this->head = $this->head->next;
            assert($this->count > 0);
            $this->count--;
            $removed++;
        }

        // Remove matching nodes in the rest of the list
        $current = $this->head;
        while ($current !== null && $current->next !== null) {
            if ($this->comparator->compare($current->next->value, $value) === 0) {
                $current->next = $current->next->next;
                assert($this->count > 0);
                $this->count--;
                $removed++;
            } else {
                if ($this->comparator->compare($current->next->value, $value) > 0) {
                    break; // sorted: no more matches possible
                }
                $current = $current->next;
            }
        }

        return $removed;
    }

    /**
     * Check whether a value exists in the list.
     *
     * @param T $value
     * @throws InvalidArgumentException if the value type is incompatible with existing elements
     */
    public function contains(int|string $value): bool
    {
        $this->assertCompatibleType($value);

        $current = $this->head;
        while ($current !== null) {
            if ($this->comparator->compare($current->value, $value) === 0) {
                return true;
            }
            if ($this->comparator->compare($current->value, $value) > 0) {
                break;
            }
            $current = $current->next;
        }

        return false;
    }

    /**
     * Return all values as an array (in sorted order).
     *
     * @return list<T>
     */
    public function toArray(): array
    {
        $result = [];
        $current = $this->head;
        while ($current !== null) {
            $result[] = $current->value;
            $current = $current->next;
        }

        return $result;
    }

    public function clear(): void
    {
        $this->head = null;
        $this->count = 0;
    }

    public function isEmpty(): bool
    {
        return $this->head === null;
    }

    // --- Countable ---

    /** @phpstan-return int<0, max> */
    public function count(): int
    {
        return $this->count;
    }

    // --- Iterator ---

    /** @return T */
    public function current(): mixed
    {
        assert($this->current !== null);

        return $this->current->value;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->current = $this->current?->next;
        $this->position++;
    }

    public function rewind(): void
    {
        $this->current = $this->head;
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->current !== null;
    }

    // --- Private helpers ---

    private function assertCompatibleType(int|string $value): void
    {
        if ($this->head === null) {
            return;
        }

        $existingType = get_debug_type($this->head->value);
        $newType = get_debug_type($value);

        if ($existingType !== $newType) {
            throw new InvalidArgumentException("Cannot use value of type '{$newType}' with a list of '{$existingType}' values.");
        }
    }
}
