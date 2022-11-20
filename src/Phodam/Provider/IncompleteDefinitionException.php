<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use Phodam\PhodamException;
use Throwable;

class IncompleteDefinitionException extends PhodamException
{
    private string $type;

    /** @var string[] */
    private array $unmappedFields;

    /**
     * @param string $type
     * @param string[] $unmappedFields ;
     * @param ?string $message
     * @param ?Throwable $previous
     */
    public function __construct(
        string $type,
        array $unmappedFields,
        ?string $message = null,
        ?Throwable $previous = null
    ) {
        $message = $message ?? self::defaultMessage($type, $unmappedFields);

        parent::__construct($message, 0, $previous);

        $this->type = $type;
        $this->unmappedFields = $unmappedFields;
    }

    /**
     * @param string $type
     * @param string[] $unmappedFields
     */
    private static function defaultMessage(
        string $type,
        array $unmappedFields
    ): string {
        $unmappedFieldsClause = join(', ', $unmappedFields);

        return "{$type}: Unable to map fields: {$unmappedFieldsClause}";
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getUnmappedFields(): array
    {
        return $this->unmappedFields;
    }
}
