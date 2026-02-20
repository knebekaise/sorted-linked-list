<?php

declare(strict_types=1);

namespace Knebekaise\SortedLinkedList\Tests;

use InvalidArgumentException;
use Knebekaise\SortedLinkedList\CaseInsensitiveStringComparator;
use Knebekaise\SortedLinkedList\Comparator;
use Knebekaise\SortedLinkedList\DescendingIntComparator;
use Knebekaise\SortedLinkedList\SortedLinkedList;
use PHPUnit\Framework\TestCase;

final class SortedLinkedListTest extends TestCase
{
    /** @var SortedLinkedList<int|string> */
    private SortedLinkedList $list;

    protected function setUp(): void
    {
        $this->list = new SortedLinkedList();
    }

    // --- insert ---

    public function testNewListIsEmpty(): void
    {
        self::assertTrue($this->list->isEmpty());
        self::assertSame(0, $this->list->count());
        self::assertSame([], $this->list->toArray());
    }

    public function testInsertSingleInteger(): void
    {
        $this->list->insert(5);

        self::assertFalse($this->list->isEmpty());
        self::assertSame(1, $this->list->count());
        self::assertSame([5], $this->list->toArray());
    }

    public function testInsertMaintainsSortedOrder(): void
    {
        $this->list->insert(3);
        $this->list->insert(1);
        $this->list->insert(4);
        $this->list->insert(1);
        $this->list->insert(5);
        $this->list->insert(2);

        self::assertSame([1, 1, 2, 3, 4, 5], $this->list->toArray());
    }

    public function testInsertStrings(): void
    {
        $this->list->insert('banana');
        $this->list->insert('apple');
        $this->list->insert('cherry');

        self::assertSame(['apple', 'banana', 'cherry'], $this->list->toArray());
    }

    public function testInsertIncompatibleTypeThrows(): void
    {
        $this->list->insert(1);

        $this->expectException(InvalidArgumentException::class);
        $this->list->insert('hello');
    }

    // --- clear ---

    public function testClearEmptiesTheList(): void
    {
        $this->list->insert(1);
        $this->list->insert(2);
        $this->list->insert(3);

        $this->list->clear();

        self::assertTrue($this->list->isEmpty());
        self::assertSame(0, $this->list->count());
        self::assertSame([], $this->list->toArray());
    }

    public function testClearOnEmptyListIsNoOp(): void
    {
        $this->list->clear();

        self::assertTrue($this->list->isEmpty());
        self::assertSame(0, $this->list->count());
    }

    public function testInsertAfterClearWorks(): void
    {
        $this->list->insert(1);
        $this->list->clear();
        $this->list->insert(5);

        self::assertSame([5], $this->list->toArray());
    }

    // --- contains ---

    public function testContainsReturnsTrueForExistingValue(): void
    {
        $this->list->insert(10);
        $this->list->insert(20);

        self::assertTrue($this->list->contains(10));
        self::assertTrue($this->list->contains(20));
    }

    public function testContainsReturnsFalseForMissingValue(): void
    {
        $this->list->insert(10);

        self::assertFalse($this->list->contains(99));
    }

    public function testContainsOnEmptyList(): void
    {
        self::assertFalse($this->list->contains(1));
    }

    // --- removeOne ---

    public function testRemoveOneExistingValue(): void
    {
        $this->list->insert(1);
        $this->list->insert(2);
        $this->list->insert(3);

        $removed = $this->list->removeOne(2);

        self::assertTrue($removed);
        self::assertSame([1, 3], $this->list->toArray());
        self::assertSame(2, $this->list->count());
    }

    public function testRemoveOneHead(): void
    {
        $this->list->insert(1);
        $this->list->insert(2);

        $this->list->removeOne(1);

        self::assertSame([2], $this->list->toArray());
    }

    public function testRemoveOneTail(): void
    {
        $this->list->insert(1);
        $this->list->insert(2);

        $this->list->removeOne(2);

        self::assertSame([1], $this->list->toArray());
    }

    public function testRemoveOneOnlyFirstOccurrence(): void
    {
        $this->list->insert(5);
        $this->list->insert(5);

        $this->list->removeOne(5);

        self::assertSame([5], $this->list->toArray());
    }

    public function testRemoveOneMissingValueReturnsFalse(): void
    {
        $this->list->insert(1);

        self::assertFalse($this->list->removeOne(99));
    }

    public function testRemoveOneFromEmptyList(): void
    {
        self::assertFalse($this->list->removeOne(1));
    }

    // --- removeAll ---

    public function testRemoveAllRemovesSingleOccurrence(): void
    {
        $this->list->insert(1);
        $this->list->insert(2);
        $this->list->insert(3);

        $count = $this->list->removeAll(2);

        self::assertSame(1, $count);
        self::assertFalse($this->list->contains(2));
        self::assertSame([1, 3], $this->list->toArray());
    }

    public function testRemoveAllRemovesAllDuplicates(): void
    {
        $this->list->insert(1);
        $this->list->insert(2);
        $this->list->insert(2);
        $this->list->insert(3);

        $count = $this->list->removeAll(2);

        self::assertSame(2, $count);
        self::assertSame([1, 3], $this->list->toArray());
        self::assertSame(2, $this->list->count());
    }

    public function testRemoveAllMissingValueReturnsZero(): void
    {
        $this->list->insert(1);

        self::assertSame(0, $this->list->removeAll(99));
    }

    public function testRemoveAllFromEmptyList(): void
    {
        self::assertSame(0, $this->list->removeAll(1));
    }

    public function testRemoveAllHead(): void
    {
        $this->list->insert(1);
        $this->list->insert(1);
        $this->list->insert(2);

        $count = $this->list->removeAll(1);

        self::assertSame(2, $count);
        self::assertSame([2], $this->list->toArray());
    }

    public function testRemoveAllTail(): void
    {
        $this->list->insert(1);
        $this->list->insert(2);
        $this->list->insert(2);

        $count = $this->list->removeAll(2);

        self::assertSame(2, $count);
        self::assertSame([1], $this->list->toArray());
    }

    // --- type guard ---

    public function testContainsThrowsOnIncompatibleType(): void
    {
        $this->list->insert(1);

        $this->expectException(InvalidArgumentException::class);
        $this->list->contains('hello');
    }

    public function testRemoveOneThrowsOnIncompatibleType(): void
    {
        $this->list->insert(1);

        $this->expectException(InvalidArgumentException::class);
        $this->list->removeOne('hello');
    }

    public function testRemoveAllThrowsOnIncompatibleType(): void
    {
        $this->list->insert(1);

        $this->expectException(InvalidArgumentException::class);
        $this->list->removeAll('hello');
    }

    // --- custom comparator ---

    public function testCustomComparatorAffectsSortOrder(): void
    {
        $reverseOrder = new class implements Comparator {
            public function compare(int|string $a, int|string $b): int
            {
                return $b <=> $a;
            }
        };

        $list = new SortedLinkedList($reverseOrder);
        $list->insert(1);
        $list->insert(3);
        $list->insert(2);

        self::assertSame([3, 2, 1], $list->toArray());
    }

    public function testCustomComparatorAffectsContains(): void
    {
        $caseInsensitive = new class implements Comparator {
            public function compare(int|string $a, int|string $b): int
            {
                return strtolower((string) $a) <=> strtolower((string) $b);
            }
        };

        $list = new SortedLinkedList($caseInsensitive);
        $list->insert('Banana');
        $list->insert('apple');

        self::assertTrue($list->contains('APPLE'));
        self::assertSame(['apple', 'Banana'], $list->toArray());
    }

    // --- DescendingIntComparator ---

    public function testDescendingIntComparatorSortsDescending(): void
    {
        $list = new SortedLinkedList(new DescendingIntComparator());
        $list->insert(3);
        $list->insert(1);
        $list->insert(4);
        $list->insert(1);
        $list->insert(2);

        self::assertSame([4, 3, 2, 1, 1], $list->toArray());
    }

    public function testDescendingIntComparatorContains(): void
    {
        $list = new SortedLinkedList(new DescendingIntComparator());
        $list->insert(5);
        $list->insert(10);

        self::assertTrue($list->contains(5));
        self::assertTrue($list->contains(10));
        self::assertFalse($list->contains(7));
    }

    public function testDescendingIntComparatorRemoveOne(): void
    {
        $list = new SortedLinkedList(new DescendingIntComparator());
        $list->insert(3);
        $list->insert(3);
        $list->insert(1);

        self::assertTrue($list->removeOne(3));
        self::assertSame([3, 1], $list->toArray());
    }

    public function testDescendingIntComparatorRemoveAll(): void
    {
        $list = new SortedLinkedList(new DescendingIntComparator());
        $list->insert(5);
        $list->insert(3);
        $list->insert(3);
        $list->insert(1);

        self::assertSame(2, $list->removeAll(3));
        self::assertSame([5, 1], $list->toArray());
    }

    public function testDescendingIntComparatorThrowsOnString(): void
    {
        $list = new SortedLinkedList(new DescendingIntComparator());
        $list->insert('hello'); // first insert: comparator not yet invoked

        $this->expectException(InvalidArgumentException::class);
        $list->insert('world'); // second insert: comparator called, throws
    }

    // --- CaseInsensitiveStringComparator ---

    public function testCaseInsensitiveComparatorSortOrder(): void
    {
        $list = new SortedLinkedList(new CaseInsensitiveStringComparator());
        $list->insert('Banana');
        $list->insert('apple');
        $list->insert('CHERRY');

        self::assertSame(['apple', 'Banana', 'CHERRY'], $list->toArray());
    }

    public function testCaseInsensitiveComparatorContains(): void
    {
        $list = new SortedLinkedList(new CaseInsensitiveStringComparator());
        $list->insert('Hello');

        self::assertTrue($list->contains('hello'));
        self::assertTrue($list->contains('HELLO'));
        self::assertTrue($list->contains('Hello'));
        self::assertFalse($list->contains('world'));
    }

    public function testCaseInsensitiveComparatorRemoveOne(): void
    {
        $list = new SortedLinkedList(new CaseInsensitiveStringComparator());
        $list->insert('Hello');
        $list->insert('HELLO');

        self::assertTrue($list->removeOne('hello'));
        self::assertSame(1, $list->count());
    }

    public function testCaseInsensitiveComparatorRemoveAll(): void
    {
        $list = new SortedLinkedList(new CaseInsensitiveStringComparator());
        $list->insert('Hello');
        $list->insert('HELLO');
        $list->insert('world');

        self::assertSame(2, $list->removeAll('hello'));
        self::assertSame(['world'], $list->toArray());
    }

    public function testCaseInsensitiveComparatorThrowsOnInt(): void
    {
        // assertCompatibleType allows two ints through; the comparator then rejects them
        $list = new SortedLinkedList(new CaseInsensitiveStringComparator());
        $list->insert(1); // empty list: comparator not called

        $this->expectException(InvalidArgumentException::class);
        $list->insert(2); // comparator invoked with two ints → throws
    }

    // --- Iterator ---

    public function testIterationYieldsValuesInSortedOrder(): void
    {
        $this->list->insert(3);
        $this->list->insert(1);
        $this->list->insert(2);

        $collected = [];
        foreach ($this->list as $key => $value) {
            $collected[$key] = $value;
        }

        self::assertSame([1, 2, 3], $collected);
    }

    public function testIterationCanBeRepeated(): void
    {
        $this->list->insert(2);
        $this->list->insert(1);

        self::assertSame([1, 2], iterator_to_array($this->list));
        self::assertSame([1, 2], iterator_to_array($this->list));
    }
}
