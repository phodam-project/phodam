<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Phodam\Provider;

use DateTime;
use InvalidArgumentException;
use Phodam\Provider\ProviderConfig;
use Phodam\Provider\ProviderInterface;
use PhodamTests\Fixtures\SampleProvider;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(\Phodam\Provider\ProviderConfig::class)]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, '__construct')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'getProvider')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'forArray')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'withName')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'isArray')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'getType')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'getName')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'forType')]
#[CoversMethod(\Phodam\Provider\ProviderConfig::class, 'validate')]
class ProviderConfigTest extends PhodamBaseTestCase
{
    private ProviderInterface $provider;

    public function setUp(): void
    {
        $this->provider = new SampleProvider();
    }

    public function testConstruct()
    {
        $config = new ProviderConfig($this->provider);
        $this->assertEquals($this->provider, $config->getProvider());
    }

    public function testForArray()
    {
        $name = "MyArrayName";
        $config = (new ProviderConfig($this->provider))
            ->forArray()
            ->withName($name);

        $this->assertTrue($config->isArray());
        $this->assertNull($config->getType());
        $this->assertEquals($name, $config->getName());
        $this->assertEquals($this->provider, $config->getProvider());
    }

    public function testForClass()
    {
        $name = "MyDateTimeName";
        $config = (new ProviderConfig($this->provider))
            ->forType(DateTime::class)
            ->withName($name);

        $this->assertFalse($config->isArray());
        $this->assertEquals(DateTime::class, $config->getType());
        $this->assertEquals($name, $config->getName());
        $this->assertEquals($this->provider, $config->getProvider());
    }

    public function testValidate(): void
    {
        $config = (new ProviderConfig($this->provider))
            ->forType('string');

        $config->validate();

        $this->assertEquals('string', $config->getType());
    }

    public function testValidateWithArrayWithoutName(): void
    {
        $config = (new ProviderConfig($this->provider))
            ->forArray();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("An array provider config must have a name");

        $config->validate();
    }

    public function testValidateWithoutAnyValidType(): void
    {
        $config = (new ProviderConfig($this->provider));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A provider config must be declared for an array or a type");

        $config->validate();
    }
}
