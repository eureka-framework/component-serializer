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
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [];

        if (\is_iterable($this)) {
            //~ Iterate over collection when objet is iterable
            $object = \iterator_to_array($this);
        } else {
            //~ Iterate over object properties when not iterable
            $object = \get_object_vars($this);
        }

        foreach ($object as $property => $value) {
            $data[$property] = ($value instanceof \JsonSerializable) ? $value->jsonSerialize() : $value;
        }

        return $data;
    }
}
