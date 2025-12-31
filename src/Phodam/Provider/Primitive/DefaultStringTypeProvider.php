<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Primitive;

use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends string
 * @template-implements TypedProviderInterface<string>
 */
class DefaultStringTypeProvider implements TypedProviderInterface
{
    private const LOWER_CASE = 'abcdefghijklmnopqrstuvwxyz';
    private const UPPER_CASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const NUMERIC = '0123456789';
    private const ALPHABETIC = self::LOWER_CASE . self::UPPER_CASE;
    private const ALPHANUMERIC = self::LOWER_CASE . self::UPPER_CASE . self::NUMERIC;
    private const STRING_TYPES = [
        'lower' => self::LOWER_CASE,
        'upper' => self::UPPER_CASE,
        'alphabetic' => self::ALPHABETIC,
        'numeric' => self::NUMERIC,
        'alphanumeric' => self::ALPHANUMERIC
    ];

    public function create(ProviderContextInterface $context): string
    {
        $config = $context->getConfig();

        $type = $config['type'] ?? 'alphanumeric';
        $pool = self::STRING_TYPES[$type];
        $poolLength = strlen($pool);
        $minLength = $config['minLength'] ?? 16;
        $maxLength = $config['maxLength'] ?? 32;
        $length = $config['length'] ?? rand($minLength, $maxLength);

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($pool, rand(0, $poolLength - 1), 1);
        }

        return $str;
    }
}
