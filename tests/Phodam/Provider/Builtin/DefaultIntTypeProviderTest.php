<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider\Builtin;

use Phodam\PhodamInterface;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\ProviderContext;
use Phodam\Tests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultIntTypeProvider
 */
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

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
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

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
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

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
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
