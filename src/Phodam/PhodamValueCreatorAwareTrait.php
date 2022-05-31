<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

trait PhodamValueCreatorAwareTrait
{
    private PhodamValueCreatorInterface $phodam;

    public function getPhodam(): PhodamValueCreatorInterface
    {
        return $this->phodam;
    }

    public function setPhodam(PhodamValueCreatorInterface $phodam): void
    {
        $this->phodam = $phodam;
    }
}
