<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Phodam\Provider\Primitive;

use Phodam\PhodamInterface;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(\Phodam\Provider\Primitive\DefaultIntTypeProvider::class)]
#[CoversMethod(\Phodam\Provider\Primitive\DefaultIntTypeProvider::class, 'create')]
class DefaultIntTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultIntTypeProvider $provider;

    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultIntTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    public function testCreate()
    {
        $min = -10000;
        $max = 10000;

        $context = new ProviderContext($this->phodam, 'int', [], []);

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    public function testCreateWithConfigMinAndMax()
    {
        $min = -100;
        $max = 100;

        $context = new ProviderContext(
            $this->phodam,
            'int',
            [],
            ['min' => $min, 'max' => $max]
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    public function testCreateWithConfigMinAndMaxWithSmallRange()
    {
        $min = -1;
        $max = 1;

        $context = new ProviderContext(
            $this->phodam,
            'int',
            [],
            ['min' => $min, 'max' => $max]
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }
}
