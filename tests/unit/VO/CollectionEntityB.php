<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer\Tests\Unit\VO;

use Eureka\Component\Serializer\Exception\CollectionException;
use Eureka\Component\Serializer\VO\AbstractCollection;

/**
 * Class CollectionEntityB
 *
 * @author Romain Cottard
 *
 * @extends AbstractCollection<EntityB>
 */
class CollectionEntityB extends AbstractCollection
{
    /**
     * CollectionEntityB constructor.
     *
     * @phpstan-param list<array{id: int, name: string}> $dataEntitiesB
     */
    public function __construct(array $dataEntitiesB)
    {
        foreach ($dataEntitiesB as $dataEntityB) {
            $this->add(new EntityB($dataEntityB['id'], $dataEntityB['name']));
        }
    }

    /**
     * @param int|null $offset
     * @param EntityB $value
     * @return void
     * @throws CollectionException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof EntityB) {
            throw new CollectionException('Data must be an instance of ' . EntityB::class);
        }

        parent::offsetSet($offset, $value);
    }
}
