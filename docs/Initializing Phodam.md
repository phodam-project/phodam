# Setting Up Phodam

Initialize a `PhodamInterface` instance to generate test data for your application. This is essential for creating realistic test fixtures without manual data entry.

## Basic Setup

Use `PhodamSchema::withDefaults()` to create a schema with built-in providers for primitive types (`int`, `float`, `string`, `bool`) and PHP date/time types. This approach requires no configuration and enables immediate test data generation.

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();
```

Register custom providers using `registerProvider()` when you need to generate domain-specific types or require specialized data generation logic. Use `PhodamSchema::blank()` to start with an empty schema for complete control over provider registration.

### Starting with a Blank Schema

Use `PhodamSchema::blank()` when you need complete control over which providers are registered, without any default providers:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::blank();
// Register only the providers you need
$schema->registerProvider(MyCustomProvider::class);
$phodam = $schema->getPhodam();
```

This is useful when you want to avoid default providers or need a minimal setup for specific test scenarios.


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

### Example: In a Test Class

Here's how you would set it up in a PHPUnit test class:

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
        // if you have custom Providers
        $schema->registerProvider(MyCustomProvider::class);

        $this->phodam = $schema->getPhodam();
    }

    public function testSomething(): void
    {
        $value = $this->phodam->create(MyClass::class);
        // ... use $value in your test
    }
}
```


## Summary

Create a `PhodamInterface` instance via `PhodamSchema::withDefaults()` for immediate use, or customize the schema by registering providers for domain-specific types. The schema pattern allows flexible configuration while maintaining simplicity for common use cases.
