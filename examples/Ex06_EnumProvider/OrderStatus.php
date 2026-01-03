<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex06_EnumProvider;

/**
 * Example pure enum (UnitEnum) - no backing values
 */
enum OrderStatus
{
    case PENDING;
    case IN_PROGRESS;
    case COMPLETED;
    case CANCELLED;
}
