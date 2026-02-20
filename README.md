# SortedLinkedList

A PHP 8.2+ singly linked list that maintains elements in ascending sorted order. Supports pluggable comparison via the `Comparator` interface.

## Requirements

- PHP 8.2+

## Installation

This package is not published on Packagist. Install it directly from GitHub by adding the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/knebekaise/sorted-linked-list"
        }
    ],
    "require": {
        "knebekaise/sorted-linked-list": "^1.0"
    }
}
```

Then run:

```bash
composer install
```

## Usage

### Basic usage (natural order)

```php
use Knebekaise\SortedLinkedList\SortedLinkedList;

$list = new SortedLinkedList();

$list->insert(3);
$list->insert(1);
$list->insert(2);

$list->toArray(); // [1, 2, 3]
$list->count();   // 3
$list->contains(2); // true

$list->removeOne(2); // true  → [1, 3]
$list->removeAll(1); // 1     → [3]

$list->clear();
$list->isEmpty(); // true
```

Elements must all be the same type — mixing `int` and `string` throws `InvalidArgumentException`.

### Iteration

`SortedLinkedList` implements `Iterator`, so it works in `foreach` and with any standard iterator function:

```php
foreach ($list as $key => $value) {
    // values yielded in sorted order
}

iterator_to_array($list); // [1, 2, 3]
```

## API

| Method | Signature | Description |
|---|---|---|
| `insert` | `insert(int\|string $value): void` | Insert a value, maintaining sort order |
| `removeOne` | `removeOne(int\|string $value): bool` | Remove the first occurrence; returns `true` if removed |
| `removeAll` | `removeAll(int\|string $value): int` | Remove all occurrences; returns the count removed |
| `contains` | `contains(int\|string $value): bool` | Return `true` if the value exists |
| `clear` | `clear(): void` | Remove all elements |
| `isEmpty` | `isEmpty(): bool` | Return `true` if the list has no elements |
| `count` | `count(): int` | Return the number of elements (`Countable`) |
| `toArray` | `toArray(): list<int\|string>` | Return all values as an array in sorted order |

## Complexity

| Operation | Time | Notes |
|---|---|---|
| `insert` | O(n) | Traverses to find insertion point |
| `removeOne` | O(n) | Stops early when sorted order rules out further matches |
| `removeAll` | O(n) | Stops early when sorted order rules out further matches |
| `contains` | O(n) | Stops early when sorted order rules out further matches |
| `clear` | O(1) | Drops the head pointer |

All operations use O(1) extra space.

## Custom comparators

Pass any `Comparator` implementation to the constructor to control sort order.

```php
use Knebekaise\SortedLinkedList\Comparator;
use Knebekaise\SortedLinkedList\SortedLinkedList;

$list = new SortedLinkedList(new class implements Comparator {
    public function compare(int|string $a, int|string $b): int
    {
        return $b <=> $a; // descending
    }
});

$list->insert(1);
$list->insert(3);
$list->insert(2);

$list->toArray(); // [3, 2, 1]
```

### Built-in comparators

| Class | Behaviour |
|---|---|
| `NaturalComparator` | Default. Integers numerically, strings lexicographically (ascending). |
| `DescendingIntComparator` | Integers in descending order. Throws on non-integer values. |
| `CaseInsensitiveStringComparator` | Strings case-insensitively via `strcasecmp` (ascending). Throws on non-string values. |

#### `DescendingIntComparator`

```php
use Knebekaise\SortedLinkedList\DescendingIntComparator;

$list = new SortedLinkedList(new DescendingIntComparator());
$list->insert(1);
$list->insert(3);
$list->insert(2);

$list->toArray(); // [3, 2, 1]
```

#### `CaseInsensitiveStringComparator`

```php
use Knebekaise\SortedLinkedList\CaseInsensitiveStringComparator;

$list = new SortedLinkedList(new CaseInsensitiveStringComparator());
$list->insert('Banana');
$list->insert('apple');
$list->insert('CHERRY');

$list->toArray();      // ['apple', 'Banana', 'CHERRY']
$list->contains('APPLE'); // true
$list->removeAll('banana'); // removes 'Banana' → 1
```

### Implementing a custom comparator

```php
use Knebekaise\SortedLinkedList\Comparator;

final class AbsoluteValueComparator implements Comparator
{
    public function compare(int|string $a, int|string $b): int
    {
        return abs((int) $a) <=> abs((int) $b);
    }
}
```

The contract mirrors PHP's `usort` callable and the spaceship operator:
- negative → `$a` sorts before `$b`
- zero → `$a` and `$b` are considered equal
- positive → `$a` sorts after `$b`

## Development

```bash
# Run tests
composer phpunit tests

# Static analysis
composer phpstan
```
