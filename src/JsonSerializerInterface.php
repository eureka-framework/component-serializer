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
interface JsonSerializerInterface extends \JsonSerializable
{
    /**
     * @param \JsonSerializable $object
     * @return string
     * @throws SerializerException
     */
    public function serialize(\JsonSerializable $object): string;

    /**
     * @param string $json
     * @return JsonSerializerInterface
     * @throws SerializerException
     */
    public static function unserialize(string $json): JsonSerializerInterface;
}
