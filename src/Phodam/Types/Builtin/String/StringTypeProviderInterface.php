<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Types\Builtin\String;

use Phodam\Types\Builtin\BuiltinTypeProvider;

/**
 * @extends BuiltinTypeProvider<string>
 */
interface StringTypeProviderInterface extends BuiltinTypeProvider
{
    /**
     * @return string
     */
    public function create(): string;
}
