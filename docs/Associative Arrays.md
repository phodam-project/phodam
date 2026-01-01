# Creating Providers for Associative Arrays

This document describes how to create and register providers for associative arrays using Phodam. Array providers allow you to generate structured arrays with predefined fields and default values.

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

Array providers implement the `ProviderInterface` and return an associative array from their `create()` method.

### Basic Array Provider

Here's a simple example of an array provider:

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;

class UserProfileArrayProvider implements ProviderInterface
{
    public function create(ProviderContext $context): array
    {
        $defaults = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'age' => 30
        ];

        // Merge with any overrides
        return array_merge($defaults, $context->getOverrides());
    }
}
```

### Using ProviderContext to Generate Values

You can use the `ProviderContext` to generate dynamic values for array fields:

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;

class UserProfileArrayProvider implements ProviderInterface
{
    public function create(ProviderContext $context): array
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

### Using Config in Array Providers

You can access configuration passed to `createArray()` through `$context->getConfig()`:

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;

class ProductArrayProvider implements ProviderInterface
{
    public function create(ProviderContext $context): array
    {
        $config = $context->getConfig();
        
        // Default to random price if not specified
        $minPrice = $config['minPrice'] ?? 10.0;
        $maxPrice = $config['maxPrice'] ?? 1000.0;
        
        $defaults = [
            'name' => $context->getPhodam()->create('string'),
            'description' => $context->getPhodam()->create('string'),
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

Array providers must be registered with a name using `PhodamSchema`.

### Using `forArray()`

```php
use Phodam\PhodamSchema;
use Phodam\PhodamInterface;

$schema = PhodamSchema::withDefaults();

// Register an array provider
$schema->forType('array')
    ->withName('userProfile')
    ->registerProvider(new UserProfileArrayProvider());

$phodam = $schema->getPhodam();
```

### Registering with Class String

You can also register providers by passing the class name as a string:

```php
$schema = PhodamSchema::withDefaults();

$schema->forType('array')
    ->withName('userProfile')
    ->registerProvider(UserProfileArrayProvider::class);

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
$profile = $phodam->createArray('userProfile', 
    ['email' => 'custom@example.com'],  // overrides
    ['minPrice' => 50.0]                // config
);
```

### Method Signature

```php
public function createArray(
    string $name,           // The name of the array provider
    ?array $overrides = null, // Values to override in the array
    ?array $config = null    // Provider-specific configuration
): array;
```

## Complete Examples

### Example 1: User Profile Array

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;

class UserProfileArrayProvider implements ProviderInterface
{
    public function create(ProviderContext $context): array
    {
        $defaults = [
            'id' => $context->getPhodam()->create('int', null, [], ['min' => 1, 'max' => 10000]),
            'firstName' => $context->getPhodam()->create('string', null, [], ['minLength' => 3, 'maxLength' => 20]),
            'lastName' => $context->getPhodam()->create('string', null, [], ['minLength' => 3, 'maxLength' => 20]),
            'email' => $context->getPhodam()->create('string', null, [], ['type' => 'lower', 'minLength' => 10, 'maxLength' => 50]),
            'age' => $context->getPhodam()->create('int', null, [], ['min' => 18, 'max' => 100]),
            'isActive' => $context->getPhodam()->create('bool'),
            'createdAt' => $context->getPhodam()->create(\DateTimeImmutable::class)
        ];

        return array_merge($defaults, $context->getOverrides());
    }
}

// Register and use
$schema = PhodamSchema::withDefaults();
$schema->forType('array')
    ->withName('userProfile')
    ->registerProvider(new UserProfileArrayProvider());

$phodam = $schema->getPhodam();

// Generate a user profile
$profile = $phodam->createArray('userProfile');
// Returns: ['id' => 1234, 'firstName' => '...', 'lastName' => '...', ...]

// Override specific fields
$specificProfile = $phodam->createArray('userProfile', [
    'email' => 'john.doe@example.com',
    'age' => 30
]);
```

### Example 2: API Response Array with Config

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;

class ApiResponseArrayProvider implements ProviderInterface
{
    public function create(ProviderContext $context): array
    {
        $config = $context->getConfig();
        
        // Use config to determine response structure
        $includeMetadata = $config['includeMetadata'] ?? true;
        $statusCode = $config['statusCode'] ?? 200;
        
        $defaults = [
            'status' => $statusCode >= 200 && $statusCode < 300 ? 'success' : 'error',
            'statusCode' => $statusCode,
            'data' => $context->getPhodam()->create('string'),
            'timestamp' => time()
        ];
        
        if ($includeMetadata) {
            $defaults['metadata'] = [
                'version' => '1.0',
                'requestId' => $context->getPhodam()->create('string', null, [], ['length' => 32])
            ];
        }
        
        return array_merge($defaults, $context->getOverrides());
    }
}

// Register
$schema = PhodamSchema::withDefaults();
$schema->forType('array')
    ->withName('apiResponse')
    ->registerProvider(new ApiResponseArrayProvider());

$phodam = $schema->getPhodam();

// Use with config
$response = $phodam->createArray('apiResponse', [], [
    'statusCode' => 200,
    'includeMetadata' => true
]);
```

## Best Practices

### 1. Use Descriptive Provider Names

Choose names that clearly indicate the purpose of the array:

```php
// Good
$schema->forType('array')->withName('userProfile')->registerProvider(...);
$schema->forType('array')->withName('orderSummary')->registerProvider(...);

// Avoid
$schema->forType('array')->withName('array1')->registerProvider(...);
$schema->forType('array')->withName('data')->registerProvider(...);
```

### 2. Leverage ProviderContext for Dynamic Values

Use `$context->getPhodam()->create()` to generate dynamic values instead of hardcoding:

```php
// Good - generates different values each time
'id' => $context->getPhodam()->create('int', null, [], ['min' => 1, 'max' => 10000])

// Avoid - same value every time
'id' => 123
```

### 3. Handle Overrides Properly

Always merge defaults with overrides to allow customization:

```php
// Good
return array_merge($defaults, $context->getOverrides());

// Bad - ignores overrides
return $defaults;
```

### 4. Use Config for Provider Behavior

Use `$context->getConfig()` for provider-specific configuration rather than hardcoding behavior:

```php
// Good
$config = $context->getConfig();
$minAge = $config['minAge'] ?? 18;

// Less flexible
$minAge = 18; // hardcoded
```

### 5. Validate Config Values

Consider validating config values to provide helpful error messages:

```php
public function create(ProviderContext $context): array
{
    $config = $context->getConfig();
    
    if (isset($config['minPrice']) && $config['minPrice'] < 0) {
        throw new InvalidArgumentException('minPrice must be non-negative');
    }
    
    // ... rest of implementation
}
```

## Multiple Array Providers

You can register multiple array providers for different use cases:

```php
$schema = PhodamSchema::withDefaults();

// Different array structures for different purposes
$schema->forType('array')
    ->withName('userProfile')
    ->registerProvider(new UserProfileArrayProvider());

$schema->forType('array')
    ->withName('userSummary')
    ->registerProvider(new UserSummaryArrayProvider());

$schema->forType('array')
    ->withName('orderDetails')
    ->registerProvider(new OrderDetailsArrayProvider());

$phodam = $schema->getPhodam();

// Use each as needed
$profile = $phodam->createArray('userProfile');
$summary = $phodam->createArray('userSummary');
$order = $phodam->createArray('orderDetails');
```

## Error Handling

### ProviderNotFoundException

This exception is thrown when trying to use an array provider that doesn't exist:

```php
// This will throw ProviderNotFoundException if 'nonexistent' is not registered
$phodam->createArray('nonexistent');
```

### ProviderConflictException

This exception is thrown when trying to register an array provider with a name that already exists:

```php
$schema->forType('array')
    ->withName('userProfile')
    ->registerProvider(new UserProfileArrayProvider());

// This will throw ProviderConflictException
$schema->forType('array')
    ->withName('userProfile')
    ->registerProvider(new AnotherProvider());

// To override, use overriding()
$schema->forType('array')
    ->withName('userProfile')
    ->overriding()
    ->registerProvider(new AnotherProvider());
```

## Summary

- Array providers **must** be named (no default array providers)
- Implement `ProviderInterface` and return an array from `create()`
- Use `ProviderContext` to generate dynamic values
- Register with `forArray()->withName('name')->registerProvider(...)`
- Use `createArray('name', $overrides, $config)` to generate arrays
- Always merge defaults with overrides to allow customization
- Use config for provider-specific behavior

Array providers are perfect for generating test data that matches your API response structures, database records, or any other associative array format you need in your tests.

