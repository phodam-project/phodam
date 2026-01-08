<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Provider;

use Phodam\PhodamException;
use Throwable;

class CreationFailedException extends PhodamException
{
    private string $type;
    private ?string $name;

    /**
     * @param string $type
     * @param ?string $name
     * @param ?string $message
     * @param ?Throwable $previous
     */
    public function __construct(
        string $type,
        ?string $name,
        ?string $message = null,
        ?Throwable $previous = null
    ) {
        $message = $message ?? self::defaultMessage($type, $name);

        parent::__construct($message, 0, $previous);

        $this->type = $type;
        $this->name = $name;
    }

    private static function defaultMessage(string $type, ?string $name): string
    {
        if ($name !== null) {
            $providerClause = "provider named {$name}";
        } else {
            $providerClause = "default provider";
        }

        return "Creation failed for type {$type} using {$providerClause}";
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
