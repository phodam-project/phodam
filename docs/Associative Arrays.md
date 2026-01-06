# Creating Providers for Associative Arrays

This document describes how to create and register providers for associative arrays using Phodam. Array providers allow you to generate structured arrays with predefined fields and default values, useful for generating test data that matches API response structures or database records.

## Overview

Phodam provides the `createArray()` method for generating associative arrays. Unlike type providers, **array providers must be named** - there are no default array providers. This allows you to define multiple array structures, each with its own name.

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Generate an array using a named provider
$array = $phodam->createArray('userProfile');
```

## Creating an Array Provider

Array providers implement the `ProviderInterface` and return an associative array from their `create()` method. They must be registered with a name using the `#[PhodamArrayProvider]` attribute.

### Basic Implementation

```php
use Phodam\Provider\PhodamArrayProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;

#[PhodamArrayProvider('userProfile')]
class UserProfileArrayProvider implements ProviderInterface
{
    public function create(ProviderContextInterface $context): array
    {
        $defaults = [
            'firstName' => $context->getPhodam()->create('string'),
            'lastName' => $context->getPhodam()->create('string'),
            'email' => $context->getPhodam()->create('string'),
            'age' => $context->getPhodam()->create('int', null, [], ['min' => 18, 'max' => 100]),
            'isActive' => $context->getPhodam()->create('bool')
        ];

        return array_merge($defaults, $context->getOverrides());
    }
}
```

Use `$context->getPhodam()->create()` to generate dynamic values instead of hardcoding them. This ensures each generated array contains different values.

### Using Configuration

You can access configuration passed to `createArray()` through `$context->getConfig()` to customize provider behavior:

```php
#[PhodamArrayProvider('product')]
class ProductArrayProvider implements ProviderInterface
{
    public function create(ProviderContextInterface $context): array
    {
        $config = $context->getConfig();
        
        $minPrice = $config['minPrice'] ?? 10.0;
        $maxPrice = $config['maxPrice'] ?? 1000.0;
        
        $defaults = [
            'name' => $context->getPhodam()->create('string'),
            'price' => $context->getPhodam()->create('float', null, [], [
                'min' => $minPrice,
                'max' => $maxPrice,
                'precision' => 2
            ]),
            'inStock' => $context->getPhodam()->create('bool')
        ];

        return array_merge($defaults, $context->getOverrides());
    }
}
```

## Registering Array Providers

Register array providers using `registerProvider()` on your schema instance:

```php
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(UserProfileArrayProvider::class);
$phodam = $schema->getPhodam();
```

## Using Array Providers

Once registered, use `createArray()` to generate arrays:

```php
// Basic usage
$profile = $phodam->createArray('userProfile');

// With overrides
$profile = $phodam->createArray('userProfile', [
    'email' => 'custom@example.com',
    'age' => 25
]);

// With overrides and config
$profile = $phodam->createArray('product', 
    ['name' => 'Custom Product'],  // overrides
    ['minPrice' => 50.0]            // config
);
```

The method signature is:

```php
public function createArray(
    string $name,           // The name of the array provider
    ?array $overrides = null, // Values to override in the array
    ?array $config = null    // Provider-specific configuration
): array;
```

## Best Practices

### Use Descriptive Provider Names

Choose names that clearly indicate the purpose of the array:

```php
// Good
#[PhodamArrayProvider('userProfile')]
#[PhodamArrayProvider('orderSummary')]

// Avoid
#[PhodamArrayProvider('array1')]  // Not descriptive
#[PhodamArrayProvider('data')]    // Too generic
```

### Always Merge Overrides

Always merge defaults with overrides to allow customization:

```php
return array_merge($defaults, $context->getOverrides());
```

### Validate Config Values

Consider validating config values to provide helpful error messages:

```php
public function create(ProviderContextInterface $context): array
{
    $config = $context->getConfig();
    
    if (isset($config['minPrice']) && $config['minPrice'] < 0) {
        throw new InvalidArgumentException('minPrice must be non-negative');
    }
    
    // ... rest of implementation
}
```

## Error Handling

### ProviderNotFoundException

Thrown when trying to use an array provider that doesn't exist:

```php
// This will throw ProviderNotFoundException if 'nonexistent' is not registered
$phodam->createArray('nonexistent');
```

### ProviderConflictException

Thrown when trying to register an array provider with a name that already exists. Use `overriding: true` in the attribute to allow overriding:

```php
#[PhodamArrayProvider('userProfile', overriding: true)]
class ImprovedProvider implements ProviderInterface { /* ... */ }
```

## Summary

Array providers generate structured associative arrays for test data. They must be named using the `#[PhodamArrayProvider]` attribute and implement `ProviderInterface`. Use `$context->getPhodam()->create()` for dynamic values, `$context->getConfig()` for provider-specific configuration, and always merge defaults with overrides. Register providers with `registerProvider()` and generate arrays using `createArray()`. Array providers are ideal for generating test data that matches API response structures, database records, or any other associative array format needed in tests.
