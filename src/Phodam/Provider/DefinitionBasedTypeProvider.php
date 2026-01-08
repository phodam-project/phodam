<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Provider;

use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class DefinitionBasedTypeProvider implements ProviderInterface
{
    /** @var TypeDefinition<*> $definition */
    private TypeDefinition $definition;
    private bool $analyzed = false;

    /**
     * @param TypeDefinition<*> $definition
     */
    public function __construct(
        TypeDefinition $definition
    ) {
        $this->definition = $definition;
    }

    /**
     * @inheritDoc
     * @throws IncompleteDefinitionException
     * @throws ReflectionException
     */
    public function create(ProviderContextInterface $context)
    {
        // TODO: We could check if $this->type is compatible with
        // $context->getType(), but it's not as simple as checking ===.

        // okay, so here's some thoughts.
        // a definition shouldn't have to be complete, we should only HAVE
        //     to define the fields that the type analyzer can't handle
        // not sure the order on how i want to do this, but...
        // 1. get a list of class fields
        $refClass = new ReflectionClass($this->definition->getType());

        if (!$this->analyzed) {
            $this->analyze($refClass);
        }

        $generateField = function ($def) use ($context) {
            return $context->getPhodam()->create(
                $def->getType(),
                $def->getName() ?? null,
                $def->getOverrides() ?? null,
                $def->getConfig() ?? null
            );
        };

        $obj = $refClass->newInstanceWithoutConstructor();
        foreach ($this->definition->getFields() as $fieldName => $fieldDefinition) {
            /** @var FieldDefinition<*> $fieldDefinition */
            $refProperty = $refClass->getProperty($fieldName);
            if ($context->hasOverride($fieldName)) {
                $val = $context->getOverride($fieldName);
            } else {
                if ($fieldDefinition->isArray()) {
                    $val = array_map(
                        fn () => $generateField($fieldDefinition),
                        range(1, rand(2, 5))
                    );
                } else {
                    $val = $generateField($fieldDefinition);
                }
            }
            $refProperty->setValue($obj, $val);
        }

        return $obj;
    }

    /**
     * @param ReflectionClass<*> $refClass
     * @throws TypeAnalysisException|IncompleteDefinitionException
     * @throws ReflectionException
     */
    private function analyze(ReflectionClass $refClass): void
    {
        $classFields = array_map(
            fn (ReflectionProperty $refProperty) => $refProperty->getName(),
            $refClass->getProperties()
        );

        // 2. get a list of definition fields
        $defFields = $this->definition->getFieldNames();

        // 3. check to see which fields don't overlap
        $missingFields = array_diff($classFields, $defFields);
        // 4. if the definition handles it all, then that's fine
        if (!empty($missingFields)) {
            // 5. if it doesn't, start up a type analyzer
            $analyzer = new TypeAnalyzer();
            $generatedDefFields = [];
            try {
                // 6. generate a definition for the type analyzer
                $generatedDef = $analyzer->analyze($this->definition->getType());
                $generatedDefFields = $generatedDef->getFields();
            } catch (TypeAnalysisException $ex) {
                // 7. if it throws an exception, check the $mappedFields from
                //    the exception
                $generatedDefFields = $ex->getMappedFields();
            }

            $stillMissingFields = array_diff(
                $missingFields,
                array_keys($generatedDefFields)
            );
            if (!empty($stillMissingFields)) {
                // 9. if it doesn't, then throw an exception and give up
                throw new IncompleteDefinitionException(
                    $this->definition->getType(),
                    $stillMissingFields
                );
            }

            // 8. if $mappedFields covers the difference in fields,
            //    then you're good
            foreach ($missingFields as $missingField) {
                $this->definition->addFieldDefinition($missingField, $generatedDefFields[$missingField]);
            }
        }

        $this->analyzed = true;
    }
}
