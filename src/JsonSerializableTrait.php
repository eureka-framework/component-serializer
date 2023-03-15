<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer;

/**
 * Trait JsonSerializableTrait
 *
 * @author Romain Cottard
 */
trait JsonSerializableTrait
{
    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [];
        /** @var iterable $this */
        foreach ($this as $property => $value) {
            $data[$property] = ($value instanceof \JsonSerializable) ? $value->jsonSerialize() : $value;
        }

        return $data;
    }
}
