<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider;

use Phodam\Provider\IncompleteDefinitionException;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\IncompleteDefinitionException
 */
class IncompleteDefinitionExceptionTest extends PhodamBaseTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getType
     */
    public function testConstruct(): void
    {
        $message = 'My Message Here';
        $type = SimpleType::class;
        $unmappedFields = ['hi', 'two'];

        $ex = new IncompleteDefinitionException(
            $type,
            $unmappedFields,
            $message
        );

        $this->assertEquals($type, $ex->getType());
        $this->assertEquals($message, $ex->getMessage());
    }
}
