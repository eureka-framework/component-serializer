<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer\Tests;

use Eureka\Component\Serializer\Exception\SerializerException;
use Eureka\Component\Serializer\JsonSerializer;
use Eureka\Component\Serializer\Tests\VO\CollectionEntityB;
use Eureka\Component\Serializer\Tests\VO\EntityA;
use Eureka\Component\Serializer\Tests\VO\EntityB;
use PHPUnit\Framework\TestCase;

/**
 * Class SerializerTest
 *
 * @author Romain Cottard
 */
class SerializerTest extends TestCase
{
    /**
     * @param \JsonSerializable $originalVO
     * @return void
     * @throws SerializerException
     *
     * @dataProvider provideVOForSerializationAndUnSerializationTests
     */
    public function testICanSerializeAndDeserializeAValueObject(\JsonSerializable $originalVO): void
    {
        //~ Serializer / Unserializer service
        $serializer = new JsonSerializer();

        //~ Serialize VO
        $json = $serializer->serialize($originalVO);

        //~ Unserialize
        $unserializedVO = $serializer->unserialize($json, get_class($originalVO));

        //~ Compare data
        $this->assertEquals($originalVO, $unserializedVO);
    }

    /**
     * @return void
     * @throws SerializerException
     * @throws \JsonException
     */
    public function testASerializerExceptionIsThrownWhenTheUnserializedStringHasAnUnsupportedField(): void
    {
        $data = ['id' => 1, 'name' => 'name A#1', 'other' => 'any value'];

        $this->expectException(SerializerException::class);
        (new JsonSerializer())->unserialize(json_encode($data, JSON_THROW_ON_ERROR), VO\EntityA::class);
    }

    /**
     * @return void
     * @throws SerializerException
     */
    public function testASerializerExceptionIsThrownWhenISerializeInvalidData(): void
    {
        $this->expectException(SerializerException::class);
        (new JsonSerializer())->serialize(
            new class implements \JsonSerializable {
                public function jsonSerialize(): string
                {
                    return "\xB1\x31";
                }
            }
        );
    }

    /**
     * @return void
     * @throws SerializerException
     */
    public function testASerializerExceptionIsThrownWhenIUnserializeAnInvalidJson(): void
    {
        $this->expectException(SerializerException::class);
        (new JsonSerializer())->unserialize('[', VO\EntityA::class);
    }

    /**
     * @return void
     * @throws SerializerException
     */
    public function testASerializerExceptionIsThrownWhenIUnserializeDataAndTryToMapToANonExistingClass(): void
    {
        $this->expectException(SerializerException::class);

        (new JsonSerializer())->unserialize('[]', 'Test\Hello\Not\Exists');
    }

    /**
     * Data provider for success tests.
     *
     * @return array<string, list<EntityA|EntityB>>
     */
    public function provideVOForSerializationAndUnserializationTests(): array
    {
        $collection = [
            ['id' => 1, 'name' => 'name B #1'],
            ['id' => 2, 'name' => 'name B #2'],
            ['id' => 3, 'name' => 'name B #3'],
        ];

        return [
            'Entity A VO'                 => [new VO\EntityA(42, 'name A', null)],
            'Entity B VO'                 => [new VO\EntityB(43, 'name B')],
            'Entity A with Collection VO' => [new VO\EntityA(42, 'name A', new CollectionEntityB($collection))],
        ];
    }
}
