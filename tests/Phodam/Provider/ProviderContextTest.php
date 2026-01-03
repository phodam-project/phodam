<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Provider;

use InvalidArgumentException;
use Phodam\PhodamInterface;
use Phodam\Provider\ProviderContext;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

#[CoversClass(\Phodam\Provider\ProviderContext::class)]
#[CoversMethod(\Phodam\Provider\ProviderContext::class, 'getType')]
#[CoversMethod(\Phodam\Provider\ProviderContext::class, 'getOverrides')]
#[CoversMethod(\Phodam\Provider\ProviderContext::class, 'hasOverride')]
#[CoversMethod(\Phodam\Provider\ProviderContext::class, 'getOverride')]
#[CoversMethod(\Phodam\Provider\ProviderContext::class, 'getConfig')]
#[CoversMethod(\Phodam\Provider\ProviderContext::class, 'getPhodam')]
class ProviderContextTest extends PhodamBaseTestCase
{
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->phodam = $this->createMock(PhodamInterface::class);
    }


    public static function provideTypes(): array
    {
        return [
            'int type' => [
                'type' => 'int',
            ],
            'float type' => [
                'type' => 'float',
            ],
            'object type' => [
                'type' => SimpleType::class,
            ],
            'array type' => [
                'type' => 'array',
            ],
        ];
    }

    #[DataProvider('provideTypes')]
    public function testGetType(string $type): void
    {
        $context = new ProviderContext($this->phodam, $type, [], []);
        $this->assertSame($type, $context->getType());
    }


    public static function provideOverridesWithKeyPresent(): array
    {
        return [
            'one override get foo' => [
                'overrides' => ['foo' => 42],
                'key' => 'foo',
                'expectedValue' => 42,
            ],
            'three overrides get foo' => [
                'overrides' => [
                    'foo' => 42,
                    'bar' => 'some string',
                    'baz' => ['alpha', 'beta'],
                ],
                'key' => 'foo',
                'expectedValue' => 42,
            ],
            'three overrides get bar' => [
                'overrides' => [
                    'foo' => 42,
                    'bar' => 'some string',
                    'baz' => ['alpha', 'beta'],
                ],
                'key' => 'bar',
                'expectedValue' => 'some string',
            ],
            'three overrides get baz' => [
                'overrides' => [
                    'foo' => 42,
                    'bar' => 'some string',
                    'baz' => ['alpha', 'beta'],
                ],
                'key' => 'baz',
                'expectedValue' => ['alpha', 'beta'],
            ],
            'one null override get foo' => [
                'overrides' => ['foo' => null],
                'key' => 'foo',
                'expectedValue' => null
            ],
            'two null override get foo' => [
                'overrides' => ['foo' => null, 'bar' => null],
                'key' => 'foo',
                'expectedValue' => null
            ],
            'two null override get bar' => [
                'overrides' => ['foo' => null, 'bar' => null],
                'key' => 'bar',
                'expectedValue' => null
            ],
            'mixed null non-null override get foo' => [
                'overrides' => ['foo' => null, 'bar' => 42],
                'key' => 'foo',
                'expectedValue' => null
            ],
            'mixed null non-null override get bar' => [
                'overrides' => ['foo' => null, 'bar' => 42],
                'key' => 'bar',
                'expectedValue' => 42
            ],
            'override with objects get foo' => [
                'overrides' => [
                    'foo' => ($foo = new stdClass()),
                    'bar' => new stdClass(),
                ],
                'key' => 'foo',
                'expectedValue' => $foo,
            ],
            'override with objects get foo' => [
                'overrides' => [
                    'foo' => new stdClass(),
                    'bar' => ($bar = new stdClass()),
                ],
                'key' => 'bar',
                'expectedValue' => $bar,
            ],
        ];
    }

    public static function provideOverridesWithKeyMissing(): array
    {
        return [
            'no overrides get foo' => [
                'overrides' => [],
                'key' => 'foo',
                'expectedMessage' => 'No override for field foo',
            ],
            'one override get bar' => [
                'overrides' => ['foo' => 42],
                'key' => 'bar',
                'expectedMessage' => 'No override for field bar',
            ],
        ];
    }

    #[DataProvider('provideOverridesWithKeyPresent')]
    public function testGetOverrides(array $overrides, string $key, mixed $expectedValue): void
    {
        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            $overrides,
            []
        );
        $this->assertSame($overrides, $context->getOverrides());
    }

    #[DataProvider('provideOverridesWithKeyPresent')]
    public function testHasOverrideTrue(
        array $overrides,
        string $key,
        mixed $expectedValue
    ): void {
        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            $overrides,
            []
        );
        $this->assertTrue($context->hasOverride($key));
    }

    #[DataProvider('provideOverridesWithKeyPresent')]
    public function testGetOverrideSuccess(
        array $overrides,
        string $key,
        $expectedValue
    ): void {
        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            $overrides,
            []
        );
        $this->assertSame($expectedValue, $context->getOverride($key));
    }

    #[DataProvider('provideOverridesWithKeyMissing')]
    public function testGetOverrideFailure(
        array $overrides,
        string $key,
        string $expectedMessage
    ): void {
        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            $overrides,
            []
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $context->getOverride($key);
    }

    #[DataProvider('provideOverridesWithKeyMissing')]
    public function testHasOverrideFalse(
        array $overrides,
        string $key,
        string $expectedMessage
    ): void {
        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            $overrides,
            []
        );
        $this->assertFalse($context->hasOverride($key));
    }


    public static function provideConfigs(): array
    {
        return [
            'empty config' => [
                'config' => [],
            ],
            'basic config' => [
                'config' => [
                    'foo' => 42,
                    'bar' => 'some string',
                    'baz' => ['alpha', 'beta'],
                ],
            ],
            'config with object' => [
                'config' => [
                    'foo' => new stdClass(),
                    'bar' => new stdClass(),
                ],
            ],
        ];
    }

    #[DataProvider('provideConfigs')]
    public function testGetConfig(array $config): void
    {
        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            [],
            $config
        );
        $this->assertSame($config, $context->getConfig());
    }

    public function testGetPhodam(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            [],
            []
        );
        $this->assertSame($this->phodam, $context->getPhodam());
    }
}
