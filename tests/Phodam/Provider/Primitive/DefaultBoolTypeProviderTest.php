<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Primitive;

use Phodam\PhodamInterface;
use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(\Phodam\Provider\Primitive\DefaultBoolTypeProvider::class)]
#[CoversMethod(\Phodam\Provider\Primitive\DefaultBoolTypeProvider::class, 'create')]
class DefaultBoolTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultBoolTypeProvider $provider;

    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultBoolTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    public function testCreate()
    {
        $context = new ProviderContext($this->phodam, 'bool', [], []);

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsBool($value);
        }
    }
}
