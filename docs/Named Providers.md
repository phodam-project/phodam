# Named Providers

Named providers allow you to register multiple providers for the same type, each with a unique name. This is useful when you need different ways to generate instances of the same type, or when you need to register array providers (which must be named).

## Overview

Phodam supports two types of providers:

1. **Default Providers** - One provider per type, used when no name is specified
2. **Named Providers** - Multiple providers per type, each identified by a unique name

Named providers are particularly useful for:
- Arrays (which **must** be named - there are no default array providers)
- Having multiple generation strategies for the same class
- Creating different variations of the same type (e.g., "activeUser", "inactiveUser")

## Registering Named Providers

Named providers are registered using the `PhodamSchema` fluent API through the `Registrar` class.

### Registering a Named Provider for a Type

To register a named provider for a specific type, use `forType()` followed by `withName()` and `registerProvider()`:

```php
use Phodam\PhodamSchema;
use Phodam\PhodamInterface;

$schema = PhodamSchema::withDefaults();

// Register a named provider for a class
$schema->forType(MyClass::class)
    ->withName('activeInstance')
    ->registerProvider(new MyActiveClassProvider());

$phodam = $schema->getPhodam();
```

### Registering a Named Provider for an Array

Array providers **must** be named (you cannot register a default array provider). Use `forArray()` instead of `forType()`:

```php
use Phodam\PhodamSchema;
use Phodam\PhodamInterface;

$schema = PhodamSchema::withDefaults();

// Register a named array provider
$schema->forArray()
    ->withName('userProfile')
    ->registerProvider(new UserProfileArrayProvider());

$phodam = $schema->getPhodam();
```

Alternatively, you can use `forType('array')`:

```php
$schema->forType('array')
    ->withName('userProfile')
    ->registerProvider(new UserProfileArrayProvider());
```

## Using Named Providers

### Using a Named Type Provider

To use a named provider when creating an instance, pass the name as the second parameter to `create()`:

```php
// Use the default provider (no name)
$defaultUser = $phodam->create(User::class);

// Use a named provider
$activeUser = $phodam->create(User::class, 'activeInstance');
```

### Using a Named Array Provider

To use a named array provider, pass the name to `createArray()`:

```php
// Create an array using the named provider
$profile = $phodam->createArray('userProfile');

// With overrides
$profile = $phodam->createArray('userProfile', ['email' => 'custom@example.com']);

// With overrides and config
$profile = $phodam->createArray('userProfile', ['email' => 'custom@example.com'], ['minAge' => 18]);
```

## Complete Examples

### Example 1: Multiple Providers for the Same Class

This example shows how to have different generation strategies for the same class:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

// Define providers for different user states
class ActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): User
    {
        return new User(
            $context->create('string'),  // name
            $context->create('string'),  // email
            true                          // active = true
        );
    }
}

class InactiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): User
    {
        return new User(
            $context->create('string'),  // name
            $context->create('string'),  // email
            false                         // active = false
        );
    }
}

// Register both providers
$schema = PhodamSchema::withDefaults();

$schema->forType(User::class)
    ->withName('active')
    ->registerProvider(new ActiveUserProvider());

$schema->forType(User::class)
    ->withName('inactive')
    ->registerProvider(new InactiveUserProvider());

$phodam = $schema->getPhodam();

// Use them
$activeUser = $phodam->create(User::class, 'active');
// $activeUser->isActive() will always be true

$inactiveUser = $phodam->create(User::class, 'inactive');
// $inactiveUser->isActive() will always be false
```

### Example 2: Array Provider

This example shows how to create and use a named array provider:

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
            'firstName' => $context->create('string'),
            'lastName' => $context->create('string'),
            'email' => $context->create('string'),
            'age' => $context->create('int', null, [], ['min' => 18, 'max' => 100])
        ];

        // Merge with any overrides
        return array_merge($defaults, $context->getOverrides());
    }
}

// Register the array provider
$schema = PhodamSchema::withDefaults();

$schema->forArray()
    ->withName('userProfile')
    ->registerProvider(new UserProfileArrayProvider());

$phodam = $schema->getPhodam();

// Use it
$profile = $phodam->createArray('userProfile');
// Returns: ['firstName' => '...', 'lastName' => '...', 'email' => '...', 'age' => 25]

// With overrides
$profile = $phodam->createArray('userProfile', ['email' => 'john@example.com']);
// Returns: ['firstName' => '...', 'lastName' => '...', 'email' => 'john@example.com', 'age' => 25]
```

### Example 3: Registering with Class String

You can also register providers by passing the class name as a string, and Phodam will instantiate it:

```php
$schema = PhodamSchema::withDefaults();

// Register by class name string (must be a class implementing ProviderInterface)
$schema->forType(User::class)
    ->withName('admin')
    ->registerProvider(AdminUserProvider::class);

$phodam = $schema->getPhodam();
```

## Overriding Existing Providers

If you need to replace an existing provider (default or named), you can use the `overriding()` method before `registerProvider()`:

```php
$schema = PhodamSchema::withDefaults();

// Register a provider
$schema->forType(User::class)
    ->withName('active')
    ->registerProvider(new ActiveUserProvider());

// Later, replace it with a new provider
$schema->forType(User::class)
    ->withName('active')
    ->overriding()  // This allows overriding the existing provider
    ->registerProvider(new ImprovedActiveUserProvider());
```

**Note:** Without `overriding()`, attempting to register a provider with a name that already exists will throw a `ProviderConflictException`.

## Provider Registration Methods

The `Registrar` class (returned by `forType()` and `forArray()`) provides a fluent API:

| Method | Description |
|--------|-------------|
| `withName(string $name)` | Sets the name for a named provider. Required for named providers. |
| `registerProvider($providerOrClass)` | Registers the provider. Can accept an instance or class name string. |
| `overriding()` | Allows overriding an existing provider with the same name. |
| `registerDefinition(TypeDefinition $definition)` | Registers a type definition instead of a provider (advanced usage). |

## Default vs Named Providers

### Default Providers

- Only one default provider per type
- Used when `create()` is called without a name parameter
- Cannot have a default provider for arrays

```php
// Register a default provider
$schema->forType(User::class)
    ->registerProvider(new DefaultUserProvider());

// Use it (no name needed)
$user = $phodam->create(User::class);
```

### Named Providers

- Multiple named providers per type
- Must specify the name when using them
- Arrays **must** use named providers
- You can have both default and named providers for the same type

```php
// Register a default provider
$schema->forType(User::class)
    ->registerProvider(new DefaultUserProvider());

// Register named providers
$schema->forType(User::class)
    ->withName('admin')
    ->registerProvider(new AdminUserProvider());

$schema->forType(User::class)
    ->withName('guest')
    ->registerProvider(new GuestUserProvider());

// Use them
$defaultUser = $phodam->create(User::class);        // Uses default provider
$adminUser = $phodam->create(User::class, 'admin'); // Uses 'admin' provider
$guestUser = $phodam->create(User::class, 'guest'); // Uses 'guest' provider
```

## Error Handling

### ProviderNotFoundException

This exception is thrown when trying to use a named provider that doesn't exist:

```php
// This will throw ProviderNotFoundException if 'nonexistent' is not registered
$phodam->create(User::class, 'nonexistent');
```

### ProviderConflictException

This exception is thrown when trying to register a provider with a name that already exists (unless using `overriding()`):

```php
$schema->forType(User::class)
    ->withName('active')
    ->registerProvider(new ActiveUserProvider());

// This will throw ProviderConflictException
$schema->forType(User::class)
    ->withName('active')
    ->registerProvider(new AnotherActiveUserProvider());

// This will work (overrides the existing provider)
$schema->forType(User::class)
    ->withName('active')
    ->overriding()
    ->registerProvider(new AnotherActiveUserProvider());
```

## Best Practices

1. **Use descriptive names**: Choose names that clearly indicate the purpose of the provider (e.g., `'activeUser'` instead of `'user1'`)

2. **Arrays must be named**: Remember that array providers require a name - you cannot register a default array provider

3. **Combine with overrides and config**: Named providers work seamlessly with overrides and config parameters:

```php
$user = $phodam->create(User::class, 'admin', ['name' => 'John'], ['role' => 'super']);
```

4. **Use for testing scenarios**: Named providers are excellent for creating different test scenarios:

```php
$schema->forType(Order::class)
    ->withName('pending')
    ->registerProvider(new PendingOrderProvider());

$schema->forType(Order::class)
    ->withName('completed')
    ->registerProvider(new CompletedOrderProvider());

// In tests
$pendingOrder = $phodam->create(Order::class, 'pending');
$completedOrder = $phodam->create(Order::class, 'completed');
```

