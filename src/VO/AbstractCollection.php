<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer\VO;

use Eureka\Component\Serializer\JsonSerializableTrait;

/**
 * Class AbstractCollection
 *
 * @author Romain Cottard
 *
 * @template TValue of object
 * @implements \ArrayAccess<int, TValue>
 * @implements \Iterator<int, TValue>
 */
class AbstractCollection implements \JsonSerializable, \ArrayAccess, \Iterator, \Countable
{
    use JsonSerializableTrait {
        JsonSerializableTrait::jsonSerialize as parentJsonSerialize;
    }

    /** @phpstan-var array<int, TValue> $collection Mixed elements (mainly list of same VO) */
    private array $collection;
    private int $index = 0;

    /** @phpstan-var list<int> $mapIndex */
    private array $mapIndex = [];

    /**
     * @phpstan-param TValue $value
     */
    protected function add(object $value): void
    {
        $this->collection[] = $value;
        $this->mapIndex[]   = $this->getNewIndex();
    }

    /**
     * Return number of item in collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * Return the key of the current element
     *
     * @link https://php.net/manual/en/iterator.key.php
     * @return int scalar on success, or null on failure.
     */
    public function key(): int
    {
        return $this->mapIndex[$this->index];
    }

    /**
     * Return the current element
     *
     * @link https://php.net/manual/en/iterator.current.php
     * @return TValue Can return any type.
     */
    public function current(): object
    {
        $key = $this->mapIndex[$this->index];

        return $this->collection[$key];
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Move forward to next element
     *
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Checks if current position is valid
     *
     * @link https://php.net/manual/en/iterator.valid.php
     * @return bool The return value will be cast to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid(): bool
    {
        return ($this->index < count($this->collection));
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->collection[$offset]);
    }

    /**
     * @param int $offset
     * @return TValue
     */
    public function offsetGet(mixed $offset): object
    {
        return $this->collection[$offset];
    }

    /**
     * @param int|null $offset
     * @param TValue $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);
        } else {
            $alreadyExists             = array_key_exists($offset, $this->collection);
            $this->collection[$offset] = $value;

            //~ Add mapping key if not exists
            if (!$alreadyExists) {
                $this->mapIndex[] = $offset;
            }
        }
    }

    /**
     * @param int $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);

        $mapping = [];

        foreach ($this->collection as $keyValue => $index) {
            $mapping[] = $keyValue;
        }

        $this->mapIndex = $mapping;

        $this->rewind();
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        $array = $this->parentJsonSerialize();

        //~ Reset index position
        $this->rewind();

        return $array;
    }

    /**
     * @return int
     */
    private function getNewIndex(): int
    {
        return (!empty($this->collection)) ? array_keys($this->collection)[count($this->collection) - 1] : 0;
    }
}
