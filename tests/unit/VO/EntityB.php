<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer\Tests\Unit\VO;

use Eureka\Component\Serializer\JsonSerializableTrait;

/**
 * Class EntityB
 *
 * @author Romain Cottard
 */
class EntityB implements \JsonSerializable
{
    use JsonSerializableTrait;

    private int $id;
    private string $name;

    /**
     * EntityA constructor.
     *
     * @param int $id
     * @param string $name
     */
    public function __construct(
        int $id,
        string $name,
    ) {
        $this->id   = $id;
        $this->name = $name;
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
}
