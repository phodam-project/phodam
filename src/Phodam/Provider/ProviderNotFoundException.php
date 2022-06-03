<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use Exception;

class ProviderNotFoundException extends Exception
{
    private string $type;

    /**
     * @param string $type
     */
    public function __construct(string $type, string $message = '')
    {
        parent::__construct($message);
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
