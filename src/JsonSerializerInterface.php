<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Eureka\Component\Serializer;

use Eureka\Component\Serializer\Exception\SerializerException;

/**
 * Interface JsonSerializerInterface
 *
 * @author Romain Cottard
 */
interface JsonSerializerInterface
{
    /**
     * @param \JsonSerializable $object
     * @return string
     * @throws SerializerException
     */
    public function serialize(\JsonSerializable $object): string;

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     * @throws SerializerException
     */
    public function unserialize(string $json, string $class, bool $skippableParameters = false): object;
}
