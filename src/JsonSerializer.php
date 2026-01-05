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
 * Class SerializerException
 *
 * Exception code range: 10000-10100
 * @author Romain Cottard
 */
final class JsonSerializer implements JsonSerializerInterface
{
    /**
     * @param \JsonSerializable $object
     * @return string
     * @throws SerializerException
     */
    public function serialize(\JsonSerializable $object): string
    {
        try {
            return \json_encode($object, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new SerializerException(
                '[CLI-10200] Cannot serialize data (json_encode failed)!',
                10200,
                $exception,
            );
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     * @throws SerializerException
     */
    public function unserialize(string $json, string $class, bool $skippableParameters = false): object
    {
        try {
            /** @var array<string, mixed> $data */
            $data = \json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            return $this->hydrate($class, $data, $skippableParameters);
        } catch (\JsonException $exception) {
            throw new SerializerException(
                '[CLI-10201] Cannot unserialize data (json_decode failed)!',
                10201,
                $exception,
            );
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $data
     * @return T
     * @throws SerializerException
     */
    private function hydrate(string $class, array $data, bool $skippableParameters): object
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $exception) {
            throw new SerializerException(
                "[CLI-10202] Given class does not exists! (class: '$class')",
                10202,
                $exception,
            );
        }

        if ($reflection->getConstructor() === null) {
            return new $class(); // @codeCoverageIgnore
        }

        $parameters   = $reflection->getConstructor()->getParameters();
        $nbParameters = \count($parameters);

        $orderedArguments = [];
        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();
            $argumentValue = null;

            if ($this->hasValidNamedData($parameterName, $data)) {
                $argumentValue = $this->getArgumentValueFromType($parameter, $data, $skippableParameters);
            } elseif ($this->hasValidArrayData($parameter, $nbParameters)) {
                $argumentValue = $data;
            } elseif (!$skippableParameters) {
                throw new SerializerException(
                    "[CLI-10203] Cannot deserialize object: data '$parameterName' does not exist!",
                    10203,
                );
            }

            $orderedArguments[$parameter->getPosition()] = $argumentValue;
        }

        \ksort($orderedArguments);

        return new $class(...$orderedArguments);
    }

    /**
     * @phpstan-param array<string, mixed> $data
     * @throws SerializerException
     */
    private function getArgumentValueFromType(
        \ReflectionParameter $parameter,
        array $data,
        bool $skippableParameters,
    ): mixed {
        $parameterName = $parameter->getName();
        $parameterType = $parameter->getType();

        if (!($parameterType instanceof \ReflectionNamedType) || $parameterType->isBuiltin()) {
            return $data[$parameterName];
        }

        /** @var class-string $parameterTypeName */
        $parameterTypeName = $parameterType->getName();

        try {
            $reflectionClass = new \ReflectionClass($parameterTypeName);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $exception) {
            throw new SerializerException(
                "[CLI-10204] Given class does not exists! (class: '$parameterName')",
                10204,
                $exception,
            );
            // @codeCoverageIgnoreEnd
        }

        $argumentValue = $data[$parameterName];
        if ($this->isHydratableArgument($reflectionClass, $argumentValue)) {
            $argumentValue = $this->hydrate($reflectionClass->getName(), $argumentValue, $skippableParameters);
        }

        return $argumentValue;
    }

    /**
     * @phpstan-param array<string, mixed> $data
     */
    private function hasValidNamedData(string $parameterName, array $data): bool
    {
        return \array_key_exists($parameterName, $data);
    }

    private function hasValidArrayData(\ReflectionParameter $parameter, int $nbParameters): bool
    {
        /** @var \ReflectionNamedType|\ReflectionUnionType|\ReflectionIntersectionType|null $reflectionType */
        $reflectionType = $parameter->getType();

        if ($reflectionType === null || $nbParameters !== 1) {
            return false;
        }

        $types     = $reflectionType instanceof \ReflectionNamedType ? [$reflectionType] : $reflectionType->getTypes();
        $typeHints = \array_map(
            fn(\ReflectionType $t): string => $t instanceof \ReflectionNamedType ? $t->getName() : (string) $t,
            $types,
        );

        return \in_array('array', $typeHints, true);
    }

    /**
     * @template T of object
     * @param \ReflectionClass<T> $parameterReflectionClass
     * @param mixed|array<string,mixed> $data
     * @return bool
     * @phpstan-assert-if-true array<string, mixed> $data
     */
    private function isHydratableArgument(\ReflectionClass $parameterReflectionClass, mixed $data): bool
    {
        return (
            \in_array(\JsonSerializable::class, $parameterReflectionClass->getInterfaceNames(), true)
            && \is_array($data)
        );
    }
}
