<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamExamples\Ex06_EnumProvider;

/**
 * Example int-backed enum (BackedEnum with int)
 */
enum UserRole: int
{
    case GUEST = 1;
    case USER = 2;
    case MODERATOR = 3;
    case ADMIN = 4;
    case SUPER_ADMIN = 5;
}
