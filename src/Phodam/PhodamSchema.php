<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

use InvalidArgumentException;
use Phodam\Provider\DefaultProviderBundle;
use Phodam\Provider\ProviderBundleInterface;
use Phodam\Store\ProviderStore;
use Phodam\Store\Registrar;

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
        $schema->add(DefaultProviderBundle::class);

        return $schema;
    }

    public function __construct(ProviderStore $providerStore)
    {
        $this->providerStore = $providerStore;
    }

    /**
     * @inheritDoc
     */
    public function forType(string $type): Registrar
    {
        return (new Registrar($this->providerStore))
            ->withType($type);
    }

    /**
     * @inheritDoc
     */
    public function forArray(): Registrar
    {
        // TODO: Is this method necessary? Users could just use forType('array') directly.
        return $this->forType('array');
    }

    /**
     * @inheritDoc
     */
    public function add($bundleOrClass): void
    {
        if ($bundleOrClass instanceof ProviderBundleInterface) {
            $bundle = $bundleOrClass;
        } else {
            $bundle = (new \ReflectionClass($bundleOrClass))->newInstance();

            if (!($bundle instanceof ProviderBundleInterface)) {
                throw new InvalidArgumentException(
                    "Argument must be an instance of ProviderBundleInterface or a class implementing it"
                );
            }
        }

        $bundle->register($this);
    }

    /**
     * @inheritDoc
     */
    public function getPhodam(): PhodamInterface
    {
        return new Phodam($this->providerStore);
    }
}
