<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Analyzer;

use Phodam\Analyzer\FieldDefinition;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Analyzer\FieldDefinition
 */
class FieldDefinitionTest extends PhodamBaseTestCase
{
    /**
     * @covers ::__construct
     */
    public function testDefaultConstructor(): void
    {
        $type = SimpleType::class;
        $def = new FieldDefinition($type);

        $this->assertEquals($type, $def->getType());
        $this->assertNull($def->getName());
        $this->assertIsArray($def->getConfig());
        $this->assertEmpty($def->getConfig());
        $this->assertIsArray($def->getOverrides());
        $this->assertEmpty($def->getOverrides());
        $this->assertFalse($def->isNullable());
        $this->assertFalse($def->isArray());
    }

    /**
     * @covers ::__construct
     * @covers ::setName
     * @covers ::setConfig
     * @covers ::setOverrides
     * @covers ::setNullable
     * @covers ::setArray
     * @covers ::getType
     * @covers ::getName
     * @covers ::getConfig
     * @covers ::getOverrides
     * @covers ::isNullable
     * @covers ::isArray
     */
    public function testGettersSetters(): void
    {
        $type = SimpleType::class;
        $name = 'MyName';
        $overrides = ['a' => 'b'];
        $config = ['c' => 'd'];
        $nullable = true;
        $array = true;

        $def = (new FieldDefinition($type))
            ->setName($name)
            ->setOverrides($overrides)
            ->setConfig($config)
            ->setNullable($nullable)
            ->setArray($array);

        $this->assertInstanceOf(FieldDefinition::class, $def);
        $this->assertEquals($type, $def->getType());
        $this->assertEquals($name, $def->getName());
        $this->assertIsArray($def->getOverrides());
        $this->assertEquals($type, $def->getType());
        $this->assertIsArray($def->getConfig());
        $this->assertTrue($def->isNullable());
        $this->assertTrue($def->isArray());
    }

    /**
     * @covers ::fromArray
     */
    public function testFromArray()
    {
        $type = SimpleType::class;
        $name = 'MyName';
        $overrides = ['a' => 'b'];
        $config = ['c' => 'd'];
        $nullable = true;
        $array = true;

        $defArray = [
            'type' => $type,
            'name' => $name,
            'overrides' => $overrides,
            'config' => $config,
            'nullable' => $nullable,
            'array' => $array
        ];

        $def = FieldDefinition::fromArray($defArray);
        $this->assertInstanceOf(FieldDefinition::class, $def);
        $this->assertEquals($type, $def->getType());
        $this->assertEquals($name, $def->getName());
        $this->assertIsArray($def->getOverrides());
        $this->assertEquals($type, $def->getType());
        $this->assertIsArray($def->getConfig());
        $this->assertTrue($def->isNullable());
        $this->assertTrue($def->isArray());
    }
}
