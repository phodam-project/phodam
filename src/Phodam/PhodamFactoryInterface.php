<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

use Phodam\Types\Builtin\BuiltinTypeProvider;

interface PhodamFactoryInterface
{
    /**
     * Creates an object of type T, with any values in the $overrides array
     * overriding the corresponding fields
     *
     * @template T
     * @param class-string<T> $class the class name to create
     * @param array<string, mixed> $overrides the overrides values
     * @return T|null
     */
    public function create(string $class, array $overrides = []);

    /**
     * Creates a random integer value
     *
     * @return int
     */
    public function createInt(): int;

    /**
     * Creates a random float value
     *
     * @return float
     */
    public function createFloat(): float;

    /**
     * Creates a random string value
     *
     * @return string
     */
    public function createString(): string;

    // TODO: This should have a return type of self, but I can't figure out
    //     how to get PHPStan to recognize the covariance of these two.
    /**
     * Registers a provider for a builtin PHP type
     *
     * @param BuiltinTypeProvider $builtinTypeProvider
     * @return $this
     */
    public function registerBuiltinTypeProvider(
        BuiltinTypeProvider $builtinTypeProvider
    );

//    public function registerTypeProvider(): self;

//    /**
//     * @param string $name a custom name for an array value provider
//     * @param NamedArrayProvider $arrayProvider a provider to return array values
//     *     of specific shapes
//     */
//    public function registerNamedArrayProvider(
//        string $name,
//        NamedArrayProvider $arrayProvider
//    ): self;

    /**
     * Uses a registered NamedArrayProvider to provide an associative array,
     * overriding any values with the $overrides
     *
     * @param string $name the registered name of an array from registerArrayProvider
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public function createNamedArray(
        string $name,
        array $overrides = []
    ): array;

    /**
     * Creates an associative array of a class's values with any $overrides
     * The desired result would be what you would expect from:
     * $myObj = $phodamFactory->create(MyClass::class, $myOverrides);
     * $myArr = json_decode(json_encode($myObj), true);
     *
     * @template T
     * @param class-string<T> $class
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public function createAssociativeArray(
        string $class,
        array $overrides
    ): array;
}
