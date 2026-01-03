# Setting Up Phodam

This guide explains how to set up and initialize a `PhodamInterface` instance for use in your code.

## Basic Setup

The recommended way to create a `PhodamInterface` instance is through the `PhodamSchema` class using the `withDefaults()` method:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

// Create a schema with default providers
$schema = PhodamSchema::withDefaults();

// Get the PhodamInterface instance
$phodam = $schema->getPhodam();
```

### Example: In a Test Class

Here's how you might set it up in a PHPUnit test class:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();
        $this->phodam = $schema->getPhodam();
    }

    public function testSomething(): void
    {
        $value = $this->phodam->create(MyClass::class);
        // ... use $value in your test
    }
}
```

## Customizing the Schema

You can customize the schema before creating the `PhodamInterface` instance by registering custom type providers or bundles:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

// Create a schema with defaults
$schema = PhodamSchema::withDefaults();

// Register a custom provider for a specific type
// Providers use the PhodamProvider attribute to declare their type
$schema->registerProvider(MyCustomProvider::class);

// Or register a provider instance
$schema->registerProvider(new MyCustomProvider());

// Get the PhodamInterface instance
$phodam = $schema->getPhodam();
```

## Creating a Blank Schema

If you need to start with an empty schema (without default providers), you can use `blank()`:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

// Create a blank schema (no default providers)
$schema = PhodamSchema::blank();

// Add your own provider bundles if needed
// $schema->registerBundle(MyCustomProviderBundle::class);

// Get the PhodamInterface instance
$phodam = $schema->getPhodam();
```

## What `withDefaults()` Includes

The `withDefaults()` method creates a schema that includes:
- `DefaultPrimitiveBundle` - Provides providers for primitive types: `int`, `float`, `string`, `bool`
- `DefaultBuiltinBundle` - Provides providers for built-in PHP types: `DateTime`, `DateTimeImmutable`, `DateInterval`, `DatePeriod`, `DateTimeZone`

This gives you a ready-to-use `PhodamInterface` instance that can generate values for most basic types out of the box.

## Summary

1. Use `PhodamSchema::withDefaults()` to create a schema with default providers
2. Optionally customize the schema by registering custom providers
3. Call `getPhodam()` on the schema to obtain a `PhodamInterface` instance
4. Use the `PhodamInterface` instance to create test objects with `create()`

