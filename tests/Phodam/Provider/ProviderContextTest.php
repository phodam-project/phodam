<?php

// This file is part of Phodam
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
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

/**
 * @coversDefaultClass \Phodam\Provider\ProviderContext
 */
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

    /**
     * @dataProvider provideTypes
     * @covers ::getType
     */
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

    /**
     * @dataProvider provideOverridesWithKeyPresent
     * @covers ::getOverrides
     */
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

    /**
     * @dataProvider provideOverridesWithKeyPresent
     * @covers ::hasOverride
     */
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

    /**
     * @dataProvider provideOverridesWithKeyPresent
     * @covers ::getOverride
     */
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

    /**
     * @dataProvider provideOverridesWithKeyMissing
     * @covers ::getOverride
     */
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

    /**
     * @dataProvider provideOverridesWithKeyMissing
     * @covers ::hasOverride
     */
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

    /**
     * @dataProvider provideConfigs
     * @covers ::getConfig
     */
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


    public static function provideCreateArrays(): array
    {
        return [
            'no overrides, no config' => [
                'name' => 'NoOverridesNoConfig',
                'overrides' => null,
                'config' => null,
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'simple overrides, no config' => [
                'name' => 'SimpleOverridesNoConfig',
                'overrides' => ['foo' => 42, 'bar' => 'some string'],
                'config' => null,
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'no overrides, simple config' => [
                'name' => 'NoOverridesSimpleConfig',
                'overrides' => null,
                'config' => ['hello' => 0, 'world' => 1],
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'simple overrides, simple config' => [
                'name' => 'SimpleOverridesSimpleConfig',
                'overrides' => ['foo' => 42, 'bar' => 'some string'],
                'config' => ['hello' => 0, 'world' => 1],
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'empty overrides, no config' => [
                'name' => 'EmptyOverridesNoConfig',
                'overrides' => [],
                'config' => null,
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'no overrides, empty config' => [
                'name' => 'NoOverridesEmptyConfig',
                'overrides' => null,
                'config' => [],
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'object overrides, no config' => [
                'name' => 'ObjectOverridesNoConfig',
                'overrides' => [
                    'foo' => new stdClass(),
                    'bar' => new stdClass(),
                ],
                'config' => null,
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'no overrides, object config' => [
                'name' => 'NoOverridesObjectConfig',
                'overrides' => null,
                'config' => [
                    'hello' => new stdClass(),
                    'world' => new stdClass(),
                ],
                'createdValue' => [],
                'expectedValue' => [],
            ],
            'created simple array' => [
                'name' => 'NoOverridesNoConfig',
                'overrides' => null,
                'config' => null,
                'createdValue' => ['foo' => 42, 'bar' => 'some string'],
                'expectedValue' => ['foo' => 42, 'bar' => 'some string'],
            ],
            'created array of stdClass' => [
                'name' => 'NoOverridesNoConfig',
                'overrides' => null,
                'config' => null,
                'createdValue' => [
                    'foo' => ($foo = new stdClass()),
                    'bar' => ($bar = new stdClass()),
                ],
                'expectedValue' => ['foo' => $foo, 'bar' => $bar],
            ],
            'created array of SimpleType' => [
                'name' => 'NoOverridesNoConfig',
                'overrides' => null,
                'config' => null,
                'createdValue' => [
                    'foo' => ($foo = new SimpleType()),
                    'bar' => ($bar = new SimpleType()),
                ],
                'expectedValue' => ['foo' => $foo, 'bar' => $bar],
            ],
        ];
    }

    /**
     * @dataProvider provideCreateArrays
     * @covers ::createArray
     */
    public function testCreateArray(
        string $name,
        ?array $overrides,
        ?array $config,
        array $createdValue,
        array $expectedValue
    ): void {
        $this->phodam->expects($this->once())
            ->method('createArray')
            ->with(
                $this->identicalTo($name),
                $this->identicalTo($overrides),
                $this->identicalTo($config)
            )
            ->willReturn($createdValue);

        $context = new ProviderContext($this->phodam, 'array', [], []);
        $actualValue = $context->createArray($name, $overrides, $config);

        $this->assertSame($expectedValue, $actualValue);
    }


    public static function provideCreates(): array
    {
        return [
            'no name, no overrides, no config' => [
                'type' => SimpleType::class,
                'name' => null,
                'overrides' => null,
                'config' => null,
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'no overrides, no config' => [
                'type' => SimpleType::class,
                'name' => 'NoOverridesNoConfig',
                'overrides' => null,
                'config' => null,
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'simple overrides, no config' => [
                'type' => SimpleType::class,
                'name' => 'SimpleOverridesNoConfig',
                'overrides' => ['foo' => 42, 'bar' => 'some string'],
                'config' => null,
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'no overrides, simple config' => [
                'type' => SimpleType::class,
                'name' => 'NoOverridesSimpleConfig',
                'overrides' => null,
                'config' => ['hello' => 0, 'world' => 1],
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'simple overrides, simple config' => [
                'type' => SimpleType::class,
                'name' => 'SimpleOverridesSimpleConfig',
                'overrides' => ['foo' => 42, 'bar' => 'some string'],
                'config' => ['hello' => 0, 'world' => 1],
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'empty overrides, no config' => [
                'type' => SimpleType::class,
                'name' => 'EmptyOverridesNoConfig',
                'overrides' => [],
                'config' => null,
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'no overrides, empty config' => [
                'type' => SimpleType::class,
                'name' => 'NoOverridesEmptyConfig',
                'overrides' => null,
                'config' => [],
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'object overrides, no config' => [
                'type' => SimpleType::class,
                'name' => 'ObjectOverridesNoConfig',
                'overrides' => [
                    'foo' => new stdClass(),
                    'bar' => new stdClass(),
                ],
                'config' => null,
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'no overrides, object config' => [
                'type' => SimpleType::class,
                'name' => 'NoOverridesObjectConfig',
                'overrides' => null,
                'config' => [
                    'hello' => new stdClass(),
                    'world' => new stdClass(),
                ],
                'createdValue' => null,
                'expectedValue' => null,
            ],
            'created int' => [
                'type' => 'int',
                'name' => null,
                'overrides' => null,
                'config' => null,
                'createdValue' => 42,
                'expectedValue' => 42,
            ],
            'created integer' => [
                'type' => 'integer',
                'name' => null,
                'overrides' => null,
                'config' => null,
                'createdValue' => 42,
                'expectedValue' => 42,
            ],
            'created stdClass' => [
                'type' => stdClass::class,
                'name' => null,
                'overrides' => null,
                'config' => null,
                'createdValue' => ($value = new stdClass()),
                'expectedValue' => $value,
            ],
            'created SimpleType' => [
                'type' => SimpleType::class,
                'name' => null,
                'overrides' => null,
                'config' => null,
                'createdValue' => ($value = new SimpleType()),
                'expectedValue' => $value,
            ],
        ];
    }

    /**
     * @dataProvider provideCreates
     * @covers ::create
     */
    public function testCreate(
        string $type,
        ?string $name,
        ?array $overrides,
        ?array $config,
        $createdValue,
        $expectedValue
    ): void {
        $this->phodam->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo($type),
                $this->identicalTo($name),
                $this->identicalTo($overrides),
                $this->identicalTo($config)
            )
            ->willReturn($createdValue);

        $context = new ProviderContext($this->phodam, $type, [], []);
        $actualValue = $context->create($type, $name, $overrides, $config);

        $this->assertSame($expectedValue, $actualValue);
    }
}
