<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use DateTime;
use DateTimeImmutable;
use Phodam\PhodamInterface;
use Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider
 */
class DefaultDateTimeImmutableTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultDateTimeImmutableTypeProvider $provider;

    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultDateTimeImmutableTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $context = new ProviderContext(
            $this->phodam,
            'DateTimeImmutable',
            [],
            []
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertInstanceOf(DateTimeImmutable::class, $value);
        }
    }
}
