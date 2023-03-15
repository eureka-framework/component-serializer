<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer\Tests\VO;

use Eureka\Component\Serializer\JsonSerializableTrait;

/**
 * Class EntityA
 *
 * @author Romain Cottard
 */
class EntityA implements \JsonSerializable
{
    use JsonSerializableTrait;

    private int $id;
    private string $name;
    private ?CollectionEntityB $listEntitiesB;

    /**
     * EntityA constructor.
     *
     * @param int $id
     * @param string $name
     * @param CollectionEntityB|null $listEntitiesB
     */
    public function __construct(
        int $id,
        string $name,
        ?CollectionEntityB $listEntitiesB = null
    ) {
        $this->id            = $id;
        $this->name          = $name;
        $this->listEntitiesB = $listEntitiesB;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CollectionEntityB|null
     */
    public function getListEntitiesB(): ?CollectionEntityB
    {
        return $this->listEntitiesB;
    }
}
