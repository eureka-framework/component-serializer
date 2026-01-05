<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer\Tests\Unit;

use Eureka\Component\Serializer\Tests\Unit\VO\CollectionEntityB;
use Eureka\Component\Serializer\Tests\Unit\VO\EntityB;
use PHPUnit\Framework\TestCase;

/**
 * Class SerializerTest
 *
 * @author Romain Cottard
 */
class CollectionTest extends TestCase
{
    /**
     * @return void
     */
    public function testICanIterateOnCollectionObject(): void
    {
        $collection = $this->getCollection();
        foreach ($collection as $index => $item) {
            self::assertSame($collection[$index]->getName(), $item->getName());
        }
    }

    public function testICanCountElementInCollection(): void
    {
        self::assertCount(3, $this->getCollection());
    }

    public function testICanPerformIssetOnCollectionIndex(): void
    {
        $collection = $this->getCollection();

        self::assertTrue(isset($collection[1]));
    }

    public function testICanUnsetElementFromCollection(): void
    {
        $collection = $this->getCollection();

        unset($collection[1]);
        self::assertFalse(isset($collection[1]));
    }

    public function testICanAddElementToTheEndOfTheCollection(): void
    {
        $collection   = $this->getCollection();

        $collection[] = new EntityB(41, 'New Item #41');
        self::assertTrue(isset($collection[3]));
    }

    public function testICanAddElementToTheCollectionAtTheSpecificIndexPosition(): void
    {
        //~ Re-add item to the specific position
        $collection   = $this->getCollection();

        $collection[5] = new EntityB(42, 'New Item #42');
        self::assertSame('New Item #42', $collection[5]->getName());
    }

    public function testICanOverrideAnElementInCollection(): void
    {
        $collection = $this->getCollection();

        $collection[0] = new EntityB(43, 'New Item #43');
        self::assertSame('New Item #43', $collection[0]->getName());
    }

    public function getCollection(): CollectionEntityB
    {
        return new CollectionEntityB(
            [

                ['id' => 1, 'name' => 'name B #1'],
                ['id' => 2, 'name' => 'name B #2'],
                ['id' => 3, 'name' => 'name B #3'],
            ],
        );
    }
}
