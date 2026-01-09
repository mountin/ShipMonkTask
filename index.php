<?php

declare(strict_types=1);

class SortedLinkedList
{
	private ?Element $first = null;
	private int $count = 0;
	private bool $stringMode = false;

	public function insert(int|string $item): void
	{
		$this->establishType($item);

		$newElement = new Element($item);

		if ($this->first === null) {
			$this->first = $newElement;
			$this->count++;
			return;
		}

		if ($this->isLessOrEqual($item, $this->first->data) ) {
			$newElement->successor = $this->first;
			$this->first = $newElement;
			$this->count++;
			return;
		}

		$current = $this->first;
		while ($current->successor !== null && $this->isLessOrEqual($current->successor->data, $item)) {
			$current = $current->successor;
		}

		$newElement->successor = $current->successor;
		$current->successor = $newElement;
		$this->count++;
	}

	public function insertMany(array $items): void
	{
		foreach ($items as $item) {
			$this->insert($item);
		}
	}

	public function contains(int|string $item): bool
	{
		$current = $this->first;
		while ($current !== null) {
			if ($current->data === $item) {
				return true;
			}
			$current = $current->successor;
		}
		return false;
	}

	public function delete(int|string $item): bool
	{
		if ($this->first === null) {
			return false;
		}

		if ($this->first->data === $item) {
			$this->first = $this->first->successor;
			$this->count--;
			return true;
		}

		$current = $this->first;
		while ($current->successor !== null) {
			if ($current->successor->data === $item) {
				$current->successor = $current->successor->successor;
				$this->count--;
				return true;
			}
			$current = $current->successor;
		}

		return false;
	}

	public function deleteAll(int|string $item): int
	{
		$removed = 0;
		while ($this->delete($item)) {
			$removed++;
		}
		return $removed;
	}

	public function at(int $position): int|string
	{
		if ($position < 0 || $position >= $this->count) {
			throw new OutOfRangeException("Position $position is outside valid range [0.." . ($this->count-1) . "]");
		}

		$current = $this->first;
		for ($i = 0; $i < $position; $i++) {
			$current = $current->successor;
		}

		return $current->data;
	}

	public function isEmpty(): bool
	{
		return $this->count === 0;
	}

	public function length(): int
	{
		return $this->count;
	}

	public function clear(): void
	{
		$this->first = null;
		$this->count = 0;
		$this->stringMode = false;
	}

	public function toArray(): array
	{
		$result = [];
		$current = $this->first;
		while ($current !== null) {
			$result[] = $current->data;
			$current = $current->successor;
		}
		return $result;
	}

	public function getIterator(): Traversable
	{
		$current = $this->first;
		while ($current !== null) {
			yield $current->data;
			$current = $current->successor;
		}
	}

	// ──  helpers ──────────────────────

	private function establishType(int|string $item): void
	{
		$isStringValue = is_string($item);

		if ($this->count === 0) {
			$this->stringMode = $isStringValue;
			return;
		}

		if ($isStringValue !== $this->stringMode) {
			throw new InvalidArgumentException(
				sprintf(
					"Type mismatch: cannot add %s when list contains %s values",
					$isStringValue ? 'string' : 'integer',
					$this->stringMode ? 'string' : 'integer'
				)
			);
		}
	}

	private function isLessOrEqual(int|string $a, int|string $b): bool
	{
		return $this->stringMode ? strcmp($a, $b) <= 0 : $a <= $b;
	}
}

final class Element
{
	public function __construct( public readonly int|string $data, public ?Element $successor = null) {}
}


$list = new SortedLinkedList();

// Numbers
$list->insert(33);
$list->insert(7);
$list->insert(15);
$list->insert(3);
$list->insert(33);         // duplicates allowed

print_r($list->toArray());
// [3, 7, 15, 33, 33]


// Strings
$fruits = new SortedLinkedList();

$fruits->insert("watermelon");
$fruits->insert("banana");
$fruits->insert("apple");
$fruits->insert("kiwi");
$fruits->insert("cherry");
$fruits->insert("banana");

echo "Fruits:\n";
print_r($fruits->toArray());
