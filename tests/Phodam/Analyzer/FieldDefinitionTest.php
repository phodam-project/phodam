<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Analyzer;

use Phodam\Types\FieldDefinition;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(\Phodam\Types\FieldDefinition::class)]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, '__construct')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'setName')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'setConfig')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'setOverrides')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'setNullable')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'setArray')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'getType')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'getName')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'getConfig')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'getOverrides')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'isNullable')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'isArray')]
#[CoversMethod(\Phodam\Types\FieldDefinition::class, 'fromArray')]
class FieldDefinitionTest extends PhodamBaseTestCase
{
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
