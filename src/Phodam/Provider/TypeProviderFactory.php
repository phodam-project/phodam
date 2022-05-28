<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use InvalidArgumentException;
use Phodam\PhodamTypes;

// TODO: This isn't actually a Factory.
//     I don't know what it is.
class TypeProviderFactory
{
    /**
     * A map of array-provider-name => TypeProviderInterface
     * @var array<string, TypeProviderInterface>
     */
    private array $arrayProviders = [];

    /**
     * A map of class-string => TypeProviderInterface
     * @var array<string, TypeProviderInterface>
     */
    private array $providers = [];

    /**
     * A map of primitive-name => TypeProviderInterface
     * @var array<string, TypeProviderInterface>
     */
    private array $primitiveProviders = [];

    /**
     * A map of class-string => { provider-name => TypeProviderInterface }
     * @var array<string, array<string, TypeProviderInterface>>
     */
    private array $namedProviders = [];

    /**
     * A map of primitive-name => { provider-name => TypeProviderInterface }
     * @var array<string, array<string, TypeProviderInterface>>
     */
    private array $namedPrimitiveProviders = [];

    public function __construct()
    {
    }

    /**
     * Registers a TypeProvider using a TypeProviderConfig
     *
     * @param TypeProviderConfig $config
     * @return void
     */
    public function registerTypeProviderConfig(TypeProviderConfig $config)
    {
        $config->validate();

        $isArray = $config->isArray();
        $primitive = $config->getPrimitive();
        $class = $config->getClass();

        if ($isArray) {
            $this->registerArrayTypeProviderConfig($config);
            return;
        }

        if ($primitive) {
            $this->registerPrimitiveTypeProviderConfig($config);
            return;
        }

        if ($class) {
            $this->registerClassTypeProviderConfig($config);
            return;
        }

        // $config->validate() should always prevent us from getting here
        throw new InvalidArgumentException(
            "Unable to determine how to register type provider"
        );
    }

    /**
     * Returns an array provider
     *
     * @param string $name the name of the array provider
     * @return TypeProviderInterface
     */
    public function getArrayProvider(string $name): TypeProviderInterface
    {
        if (!array_key_exists($name, $this->arrayProviders)) {
            throw new InvalidArgumentException(
                "Unable to find an array provider with the name {$name}"
            );
        }

        return $this->arrayProviders[$name];
    }

    /**
     * Returns a primitive provider by primitive type and optionally name
     *
     * @param string $primitive
     * @param string|null $name
     * @return TypeProviderInterface
     */
    public function getPrimitiveProvider(string $primitive, ?string $name = null): TypeProviderInterface
    {
        // make sure this is a valid primitive type
        if (!in_array($primitive, PhodamTypes::PRIMITIVE_TYPES)) {
            throw new InvalidArgumentException(
                "{$primitive} is not a valid primitive type"
            );
        }

        return $this->getNamedOrDefaultProvider(
            $this->primitiveProviders,
            $this->namedPrimitiveProviders,
            $primitive,
            $name
        );
    }

    /**
     * Returns a class provider by class name and optionally name
     *
     * @param class-string $class
     * @param string|null $name
     * @return TypeProviderInterface
     */
    public function getClassProvider(string $class, ?string $name = null): TypeProviderInterface
    {
        return $this->getNamedOrDefaultProvider(
            $this->providers,
            $this->namedProviders,
            $class,
            $name
        );
    }

    /**
     * @param array<string, TypeProviderInterface> $providers
     * @param array<string, array<string, TypeProviderInterface>> $namedProviders
     * @param string $type
     * @param string|null $name
     * @return TypeProviderInterface
     */
    private function getNamedOrDefaultProvider(
        array $providers,
        array $namedProviders,
        string $type,
        ?string $name = null
    ): TypeProviderInterface {
        // we're looking for a named provider
        if ($name) {
            if (
                !array_key_exists($type, $namedProviders)
                || !array_key_exists($name, $namedProviders[$type])
            ) {
                throw new InvalidArgumentException(
                    "Unable to find a provider of type {$type} with the name {$name}"
                );
            }
            return $namedProviders[$type][$name];
        } else {
            // looking for a default provider
            if (!array_key_exists($type, $providers)) {
                throw new InvalidArgumentException(
                    "Unable to find a default provider of type {$type}"
                );
            }
            return $providers[$type];
        }
    }

    /**
     * Registers a TypeProviderConfig for an array
     *
     * @param TypeProviderConfig $config
     * @return void
     */
    private function registerArrayTypeProviderConfig(TypeProviderConfig $config): void
    {
        if (array_key_exists($config->getName(), $this->arrayProviders)) {
            throw new InvalidArgumentException(
                "An array provider with the name {$config->getName()} already exists"
            );
        }

        $this->arrayProviders[$config->getName()] = $config->getTypeProvider();
    }

    /**
     * Registers a TypeProviderConfig for a primitive
     * @param TypeProviderConfig $config
     * @return void
     */
    private function registerPrimitiveTypeProviderConfig(TypeProviderConfig $config): void
    {
        $primitive = $config->getPrimitive();
        $name = $config->getName();
        $typeProvider = $config->getTypeProvider();

        if ($name) {
            // create the named primitive array if it doesn't exist
            if (!array_key_exists($primitive, $this->namedPrimitiveProviders)) {
                $this->namedPrimitiveProviders[$primitive] = [];
            }

            // check that we don't have a named provider for this primitive
            if (array_key_exists($name, $this->namedPrimitiveProviders[$primitive])) {
                throw new InvalidArgumentException(
                    "A primitive provider of type {$primitive} with the name {$name} already exists"
                );
            }
            $this->namedPrimitiveProviders[$primitive][$name] = $typeProvider;
        } else {
            $this->primitiveProviders[$primitive] = $typeProvider;
        }
    }

    /**
     * Registers a TypeProviderConfig for a class
     *
     * @param TypeProviderConfig $config
     * @return void
     */
    private function registerClassTypeProviderConfig(TypeProviderConfig $config): void
    {
        $class = $config->getClass();
        $name = $config->getName();
        $typeProvider = $config->getTypeProvider();

        if ($name) {
            // create the named class array if it doesn't exist
            if (!array_key_exists($class, $this->namedProviders)) {
                $this->namedProviders[$class] = [];
            }

            // check that we don't have a named provider for this class
            if (array_key_exists($name, $this->namedProviders[$class])) {
                throw new InvalidArgumentException(
                    "A class provider of type {$class} with the name {$name} already exists"
                );
            }
            $this->namedProviders[$class][$name] = $typeProvider;
        } else {
            $this->providers[$class] = $typeProvider;
        }
    }
}
