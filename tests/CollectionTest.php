<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer\Tests;

use Eureka\Component\Serializer\Tests\VO\CollectionEntityB;
use Eureka\Component\Serializer\Tests\VO\EntityB;
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
            $this->assertSame($collection[$index]->getName(), $item->getName());
        }
    }

    public function testICanCountElementInCollection(): void
    {
        $this->assertSame(3, count($this->getCollection()));
    }

    public function testICanPerformIssetOnCollectionIndex(): void
    {
        $collection = $this->getCollection();

        $this->assertTrue(isset($collection[1]));
    }

    public function testICanUnsetElementFromCollection(): void
    {
        $collection = $this->getCollection();

        unset($collection[1]);
        $this->assertFalse(isset($collection[1]));
    }

    public function testICanAddElementToTheEndOfTheCollection(): void
    {
        $collection   = $this->getCollection();

        $collection[] = new EntityB(41, 'New Item #1');
        $this->assertTrue(isset($collection[3]));
    }

    public function testICanAddElementToTheCollectionAtTheSpecificIndexPosition(): void
    {
        //~ Re-add item to the specific position
        $collection   = $this->getCollection();

        $collection[5] = new EntityB(42, 'New Item #2');
        $this->assertTrue(isset($collection[5]));
    }

    public function testICanOverrideAnElementInCollection(): void
    {
        $collection = $this->getCollection();

        $collection[0] = new EntityB(43, 'New Item #3');
        $this->assertSame('New Item #3', $collection[0]->getName());
    }

    public function getCollection(): CollectionEntityB
    {
        return new CollectionEntityB(
            [

                ['id' => 1, 'name' => 'name B #1'],
                ['id' => 2, 'name' => 'name B #2'],
                ['id' => 3, 'name' => 'name B #3'],
            ]
        );
    }
}
