<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

use InvalidArgumentException;
use Phodam\Provider\Builtin\DefaultBuiltinBundle;
use Phodam\Provider\Primitive\DefaultPrimitiveBundle;
use Phodam\Provider\DefinitionBasedTypeProvider;
use Phodam\Provider\PhodamArrayProvider;
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderBundleInterface;
use Phodam\Provider\ProviderInterface;
use Phodam\Store\ProviderStore;
use Phodam\Types\TypeDefinition;
use ReflectionClass;

class PhodamSchema implements PhodamSchemaInterface
{
    private ProviderStore $providerStore;

    public static function blank(): self
    {
        return new self(new ProviderStore());
    }

    public static function withDefaults(): self
    {
        $schema = self::blank();
        $schema->registerBundle(DefaultPrimitiveBundle::class);
        $schema->registerBundle(DefaultBuiltinBundle::class);

        return $schema;
    }

    public function __construct(ProviderStore $providerStore)
    {
        $this->providerStore = $providerStore;
    }

    /**
     * @inheritDoc
     */
    public function registerBundle($bundleOrClass): void
    {
        if ($bundleOrClass instanceof ProviderBundleInterface) {
            $bundle = $bundleOrClass;
        } else {
            $bundle = (new ReflectionClass($bundleOrClass))->newInstance();

            if (!($bundle instanceof ProviderBundleInterface)) {
                throw new InvalidArgumentException(
                    "Argument must be an instance of ProviderBundleInterface or a class implementing it"
                );
            }
        }

        $providerClasses = $bundle->getProviders();

        foreach ($providerClasses as $providerClass) {
            $this->registerProvider($providerClass);
        }

        foreach ($bundle->getTypeDefinitions() as $definitionClass) {
            $this->registerTypeDefinition($definitionClass);
        }
    }

    /**
     * Scans a provider class for attributes and registers it accordingly.
     *
     * @param ProviderInterface | class-string<ProviderInterface> $providerOrClass
     * @throws InvalidArgumentException if the provider class has no valid attribute
     */
    public function registerProvider($providerOrClass): void
    {
        if ($providerOrClass instanceof ProviderInterface) {
            $provider = $providerOrClass;
        } else {
            $provider = (new ReflectionClass($providerOrClass))->newInstance();

            if (!($provider instanceof ProviderInterface)) {
                throw new InvalidArgumentException(
                    "Argument must be an instance of ProviderInterface or a class implementing it"
                );
            }
        }

        $reflection = new ReflectionClass($provider);

        // Check for PhodamArrayProvider attribute
        $arrayProviderAttrs = $reflection->getAttributes(PhodamArrayProvider::class);
        if (!empty($arrayProviderAttrs)) {
            $attr = $arrayProviderAttrs[0];
            $args = $attr->getArguments();
            $name = $args['name'] ?? $args[0];
            $overriding = $args['overriding'] ?? $args[1] ?? false;

            if ($overriding) {
                $this->providerStore->deregisterNamedProvider('array', $name);
            }

            $this->providerStore->registerNamedProvider('array', $name, $provider);
            return;
        }

        // Check for PhodamProvider attribute
        $providerAttrs = $reflection->getAttributes(PhodamProvider::class);
        if (!empty($providerAttrs)) {
            $attr = $providerAttrs[0];
            $args = $attr->getArguments();
            $type = $args['type'] ?? $args[0];
            $name = $args['name'] ?? $args[1] ?? null;
            $overriding = $args['overriding'] ?? $args[2] ?? false;

            // $provider is already an instance if $providerOrClass was an instance
            // Only instantiate if it was a class name
            if (!($provider instanceof ProviderInterface)) {
                throw new InvalidArgumentException(
                    "Provider must implement ProviderInterface"
                );
            }

            if ($name !== null) {
                if ($overriding) {
                    $this->providerStore->deregisterNamedProvider($type, $name);
                }
                $this->providerStore->registerNamedProvider($type, $name, $provider);
            } else {
                if ($overriding) {
                    $this->providerStore->deregisterDefaultProvider($type);
                }
                $this->providerStore->registerDefaultProvider($type, $provider);
            }
            return;
        }

        $providerClass = is_object($provider) ? get_class($provider) : $provider;
        throw new InvalidArgumentException(
            "Provider class {$providerClass} must have a PhodamProvider or PhodamArrayProvider attribute"
        );
    }

    /**
     * @param TypeDefinition $definition
     */
    public function registerTypeDefinition(TypeDefinition $definition): void
    {
        $type = $definition->getType();

        if ($type === null || $type == '') {
            throw new InvalidArgumentException('TypeDefinition must have a type set');
        }

        $provider = new DefinitionBasedTypeProvider($definition);
        $name = $definition->getName();
        $overriding = $definition->isOverriding();

        if ($name !== null) {
            if ($overriding) {
                $this->providerStore->deregisterNamedProvider($type, $name);
            }
            $this->providerStore->registerNamedProvider($type, $name, $provider);
        } else {
            if ($overriding) {
                $this->providerStore->deregisterDefaultProvider($type);
            }
            $this->providerStore->registerDefaultProvider($type, $provider);
        }
    }
    /**
     * @inheritDoc
     */
    public function getPhodam(): PhodamInterface
    {
        return new Phodam($this->providerStore);
    }
}
