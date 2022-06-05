<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider\Builtin;

use Phodam\PhodamInterface;
use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\ProviderContext;
use Phodam\Tests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultBoolTypeProvider
 */
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

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreate()
    {
        $context = new ProviderContext($this->phodam, 'bool', [], []);

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsBool($value);
        }
    }
}
