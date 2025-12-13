<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use DateInterval;
use Phodam\PhodamInterface;
use Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider
 */
class DefaultDateIntervalTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultDateIntervalTypeProvider $provider;
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultDateIntervalTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $context = new ProviderContext(
            $this->phodam,
            'DateInterval',
            [],
            []
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertInstanceOf(DateInterval::class, $value);
        }
    }
}
