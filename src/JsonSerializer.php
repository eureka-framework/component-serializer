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
final class JsonSerializer
{
    /**
     * @param \JsonSerializable $object
     * @return string
     * @throws SerializerException
     */
    public function serialize(\JsonSerializable $object): string
    {
        try {
            return json_encode($object, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new SerializerException('[CLI-8200] Cannot serialize data (json_encode failed)!', 8200, $exception);
        }
    }

    /**
     * @param string $json
     * @param string $class
     * @param bool $skippableParameters
     * @return object
     * @throws SerializerException
     */
    public function unserialize(string $json, string $class, bool $skippableParameters = false)
    {
        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            return $this->hydrate($class, $data, $skippableParameters);
        } catch (\JsonException $exception) {
            throw new SerializerException('[CLI-8201] Cannot unserialize data (json_decode failed)!', 8201, $exception);
        }
    }

    /**
     * @param string $class
     * @param array $data
     * @param bool $skippableParameters
     * @return mixed
     * @throws SerializerException|\ReflectionException
     */
    private function hydrate(string $class, array $data, bool $skippableParameters)
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $exception) {
            throw new SerializerException("[CLI-8202] Given class does not exists! (class: '${class}')", 8203, $exception);
        }

        $parameters   = $reflection->getConstructor()->getParameters();
        $nbParameters = count($parameters);

        $orderedArguments = [];
        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();
            $argumentValue = null;

            if ($this->hasValidNamedData($parameterName, $data)) {
                $parameterType   = $parameter->getType();
                $reflectionClass = $parameterType instanceof \ReflectionNamedType && !$parameterType->isBuiltin()
                    ? new \ReflectionClass($parameterType->getName())
                    : null;
                $argumentValue = $data[$parameterName];
                if ($this->isHydratableArgument($reflectionClass, $argumentValue)) {
                    $argumentValue = $this->hydrate($reflectionClass->getName(), $argumentValue, $skippableParameters);
                }
            } elseif ($this->hasValidArrayData($parameter, $nbParameters)) {
                $argumentValue = $data;
            } elseif (!$skippableParameters) {
                throw new SerializerException("[CLI-8203] Cannot deserialize object: data '${parameterName}' does not exist!", 8203);
            }

            $orderedArguments[$parameter->getPosition()] = $argumentValue;
        }

        ksort($orderedArguments);

        return new $class(...$orderedArguments);
    }

    /**
     * @param string $parameterName
     * @param array $data
     * @return bool
     */
    private function hasValidNamedData(string $parameterName, array $data): bool
    {
        return array_key_exists($parameterName, $data);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @param int $nbParameters
     * @return bool
     */
    private function hasValidArrayData(\ReflectionParameter $parameter, int $nbParameters): bool
    {
        $reflectionType = $parameter->getType();

        if ($reflectionType === null || $nbParameters !== 1) {
            return false;
        }

        /* @todo Uncomment when we will support only PHP 8+ */
        /*$types = $reflectionType instanceof \ReflectionUnionType ? $reflectionType->getTypes() : [$reflectionType];*/
        /** @var \ReflectionNamedType[] $types */
        $types = [$reflectionType];

        return in_array('array', array_map(fn(\ReflectionNamedType $t): string => $t->getName(), $types));
    }

    /**
     * @param \ReflectionClass|null $parameterReflectionClass
     * @param mixed $data
     * @return bool
     */
    private function isHydratableArgument(?\ReflectionClass $parameterReflectionClass, $data): bool
    {
        return (
            $parameterReflectionClass !== null
            && in_array(\JsonSerializable::class, $parameterReflectionClass->getInterfaceNames())
            && is_array($data)
        );
    }
}
