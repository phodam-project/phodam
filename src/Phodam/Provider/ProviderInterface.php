<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use Throwable;

interface ProviderInterface
{
    /**
     * @param ProviderContext $context the context in which to create a value
     * @return mixed
     * @throws Throwable
     */
    public function create(ProviderContext $context);
}
