<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider;

use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Provider\ProviderNotFoundException;
use Phodam\Tests\Fixtures\SimpleType;
use Phodam\Tests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\ProviderNotFoundException
 */
class ProviderNotFoundExceptionTest extends PhodamBaseTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getType
     */
    public function testConstruct(): void
    {
        $message = 'My Message Here';
        $type = SimpleType::class;

        $ex = new ProviderNotFoundException(
            $type,
            $message
        );

        $this->assertEquals($type, $ex->getType());
        $this->assertEquals($message, $ex->getMessage());
    }
}