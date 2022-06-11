<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Analyzer;

class TypeAnalysisException extends \Exception
{
    private string $type;
    /** @var array<string> $fieldNames */
    private array $fieldNames = [];
    /** @var array<string, FieldDefinition> */
    private array $mappedFields = [];
    /** @var array<string> */
    private array $unmappedFields = [];

    /**
     * @param string $type
     * @param string $message
     * @param array<string> $fieldNames
     * @param array<string, FieldDefinition> $mappedFields
     * @param array<string> $unmappedFields
     */
    public function __construct(
        string $type,
        string $message = '',
        array $fieldNames = [],
        array $mappedFields = [],
        array $unmappedFields = []
    ) {
        parent::__construct($message);
        $this->type = $type;
        $this->fieldNames = $fieldNames;
        $this->mappedFields = $mappedFields;
        $this->unmappedFields = $unmappedFields;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string>
     */
    public function getFieldNames(): array
    {
        return $this->fieldNames;
    }

    /**
     * @return array<string, FieldDefinition>
     */
    public function getMappedFields(): array
    {
        return $this->mappedFields;
    }

    /**
     * @return array<string>
     */
    public function getUnmappedFields(): array
    {
        return $this->unmappedFields;
    }
}
