<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider;


use Exception;
use Phodam\Provider\CreationFailedException;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Phodam\PhodamBaseTestCase;
use Throwable;

/**
 * @coversDefaultClass \Phodam\Provider\CreationFailedException
 */
class CreationFailedExceptionTest extends PhodamBaseTestCase
{
    public function provideConstructorArgs()
    {
        return [
            'type, no name, no message, no previous' => [
                'type' => SimpleType::class,
                'name' => null,
                'message' => null,
                'expectedMessage' => (
                    'Creation failed for type PhodamTests\Fixtures\SimpleType using default provider'
                ),
                'previous' => null,
            ],
            'type, name, no message, no previous' => [
                'type' => SimpleType::class,
                'name' => 'MyProviderName',
                'message' => null,
                'expectedMessage' => (
                    'Creation failed for type PhodamTests\Fixtures\SimpleType using provider named MyProviderName'
                ),
                'previous' => null,
            ],
            'type, name, message, no previous' => [
                'type' => SimpleType::class,
                'name' => 'MyProviderName',
                'message' => 'My message here',
                'expectedMessage' => 'My message here',
                'previous' => null,
            ],
            'type, no name, no message, previous' => [
                'type' => SimpleType::class,
                'name' => null,
                'message' => null,
                'expectedMessage' => (
                    'Creation failed for type PhodamTests\Fixtures\SimpleType using default provider'
                ),
                'previous' => new Exception(),
            ],
            'type, name, no message, previous' => [
                'type' => SimpleType::class,
                'name' => 'MyProviderName',
                'message' => null,
                'expectedMessage' => (
                    'Creation failed for type PhodamTests\Fixtures\SimpleType using provider named MyProviderName'
                ),
                'previous' => new Exception(),
            ],
            'type, name, message, previous' => [
                'type' => SimpleType::class,
                'name' => 'MyProviderName',
                'message' => 'My message here',
                'expectedMessage' => 'My message here',
                'previous' => new Exception(),
            ],
        ];
    }

    /**
     * @dataProvider provideConstructorArgs
     * @covers ::__construct
     * @covers ::getType
     */
    public function testConstruct(
        string $type,
        ?string $name,
        ?string $message,
        string $expectedMessage,
        ?Throwable $previous
    ): void {
        $ex = new CreationFailedException(
            $type,
            $name,
            $message,
            $previous
        );

        // These should all be forwarded exactly.
        $this->assertSame($type, $ex->getType());
        $this->assertSame($name, $ex->getName());
        $this->assertSame($previous, $ex->getPrevious());

        // Message has a default when $message === null.
        $this->assertSame($expectedMessage, $ex->getMessage());

        // Code is not passed, but should always be 0.
        $this->assertSame(0, $ex->getCode());
    }
}
