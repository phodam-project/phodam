<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Integration\Schema;

use Phodam\Phodam;
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Store\ProviderStore;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PhodamSchema::class)]
class SchemaCreationTest extends IntegrationBaseTestCase
{
    public function testBlankCreatesNewSchema(): void
    {
        $schema = PhodamSchema::blank();

        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testWithDefaultsCreatesNewSchema(): void
    {
        $schema = PhodamSchema::withDefaults();

        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testConstructWithProviderStore(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testGetPhodamReturnsPhodamInstance(): void
    {
        $schema = PhodamSchema::withDefaults();
        $phodam = $schema->getPhodam();

        $this->assertInstanceOf(PhodamInterface::class, $phodam);
        $this->assertInstanceOf(Phodam::class, $phodam);
    }

}

