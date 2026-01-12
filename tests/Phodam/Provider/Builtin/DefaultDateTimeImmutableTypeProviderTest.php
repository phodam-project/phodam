<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Phodam\Provider\Builtin;

use DateTimeImmutable;
use Phodam\PhodamInterface;
use Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(DefaultDateTimeImmutableTypeProvider::class)]
#[CoversMethod(DefaultDateTimeImmutableTypeProvider::class, 'create')]
class DefaultDateTimeImmutableTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultDateTimeImmutableTypeProvider $provider;

    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultDateTimeImmutableTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

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
