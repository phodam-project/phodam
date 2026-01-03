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

Named providers are registered using PHP attributes on provider classes. The `PhodamProvider` attribute accepts a `name` parameter for named providers.

### Registering a Named Provider for a Type

To register a named provider for a specific type, add the `#[PhodamProvider]` attribute with a `name` parameter:

```php
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

#[PhodamProvider(MyClass::class, name: 'activeInstance')]
class MyActiveClassProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): MyClass
    {
        // Your provider implementation
    }
}

// Register the provider
use Phodam\PhodamSchema;
use Phodam\PhodamInterface;

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(MyActiveClassProvider::class);

$phodam = $schema->getPhodam();
```

### Registering a Named Provider for an Array

Array providers **must** be named (you cannot register a default array provider). Use the `#[PhodamArrayProvider]` attribute:

```php
use Phodam\Provider\PhodamArrayProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;

#[PhodamArrayProvider('userProfile')]
class UserProfileArrayProvider implements ProviderInterface
{
    public function create(ProviderContextInterface $context): array
    {
        // Your provider implementation
    }
}

// Register the array provider
$schema = PhodamSchema::withDefaults();
$schema->registerProvider(UserProfileArrayProvider::class);

$phodam = $schema->getPhodam();
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
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

#[PhodamProvider(User::class, name: 'active')]
class ActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User
    {
        return new User(
            $context->getPhodam()->create('string'),  // name
            $context->getPhodam()->create('string'),  // email
            true                          // active = true
        );
    }
}

#[PhodamProvider(User::class, name: 'inactive')]
class InactiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User
    {
        return new User(
            $context->getPhodam()->create('string'),  // name
            $context->getPhodam()->create('string'),  // email
            false                         // active = false
        );
    }
}

// Register both providers
$schema = PhodamSchema::withDefaults();
$schema->registerProvider(ActiveUserProvider::class);
$schema->registerProvider(InactiveUserProvider::class);

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
            'age' => $context->getPhodam()->create('int', null, [], ['min' => 18, 'max' => 100])
        ];

        // Merge with any overrides
        return array_merge($defaults, $context->getOverrides());
    }
}

// Register the array provider
$schema = PhodamSchema::withDefaults();
$schema->registerProvider(UserProfileArrayProvider::class);

$phodam = $schema->getPhodam();

// Use it
$profile = $phodam->createArray('userProfile');
// Returns: ['firstName' => '...', 'lastName' => '...', 'email' => '...', 'age' => 25]

// With overrides
$profile = $phodam->createArray('userProfile', ['email' => 'john@example.com']);
// Returns: ['firstName' => '...', 'lastName' => '...', 'email' => 'john@example.com', 'age' => 25]
```

### Example 3: Registering with Class String

You can register providers by passing the class name as a string, and Phodam will instantiate it:

```php
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

#[PhodamProvider(User::class, name: 'admin')]
class AdminUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User
    {
        // Implementation
    }
}

$schema = PhodamSchema::withDefaults();

// Register by class name string (must be a class implementing ProviderInterface with attribute)
$schema->registerProvider(AdminUserProvider::class);

$phodam = $schema->getPhodam();
```

## Overriding Existing Providers

If you need to replace an existing provider (default or named), you can use the `overriding` parameter in the `#[PhodamProvider]` attribute:

```php
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

// Register a provider
#[PhodamProvider(User::class, name: 'active')]
class ActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User
    {
        // Implementation
    }
}

// Later, replace it with a new provider using overriding flag
#[PhodamProvider(User::class, name: 'active', overriding: true)]
class ImprovedActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User
    {
        // Improved implementation
    }
}

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(ActiveUserProvider::class);
$schema->registerProvider(ImprovedActiveUserProvider::class);  // Overrides the previous one
```

**Note:** Without `overriding: true`, attempting to register a provider with a name that already exists will throw a `ProviderConflictException`.

## Provider Registration Methods

Providers are registered using PHP attributes and the `PhodamSchema::registerProvider()` method:

| Attribute | Description |
|-----------|-------------|
| `#[PhodamProvider(string $type, ?string $name = null, bool $overriding = false)]` | Declares a type provider. Use `name` parameter for named providers. Use `overriding: true` to override existing providers. |
| `#[PhodamArrayProvider(string $name, bool $overriding = false)]` | Declares an array provider. Array providers must have a name. |

| Method | Description |
|--------|-------------|
| `registerProvider($providerOrClass)` | Registers a provider. Can accept an instance or class name string. The provider class must have a `PhodamProvider` or `PhodamArrayProvider` attribute. |
| `registerTypeDefinition(TypeDefinition $definition)` | Registers a type definition directly (advanced usage). |

## Default vs Named Providers

### Default Providers

- Only one default provider per type
- Used when `create()` is called without a name parameter
- Cannot have a default provider for arrays

```php
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

#[PhodamProvider(User::class)]
class DefaultUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User
    {
        // Implementation
    }
}

// Register a default provider (no name in attribute)
$schema = PhodamSchema::withDefaults();
$schema->registerProvider(DefaultUserProvider::class);

// Use it (no name needed)
$user = $phodam->create(User::class);
```

### Named Providers

- Multiple named providers per type
- Must specify the name when using them
- Arrays **must** use named providers
- You can have both default and named providers for the same type

```php
#[PhodamProvider(User::class)]
class DefaultUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User { /* ... */ }
}

#[PhodamProvider(User::class, name: 'admin')]
class AdminUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User { /* ... */ }
}

#[PhodamProvider(User::class, name: 'guest')]
class GuestUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User { /* ... */ }
}

// Register providers
$schema = PhodamSchema::withDefaults();
$schema->registerProvider(DefaultUserProvider::class);
$schema->registerProvider(AdminUserProvider::class);
$schema->registerProvider(GuestUserProvider::class);

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

This exception is thrown when trying to register a provider with a name that already exists (unless using `overriding: true`):

```php
#[PhodamProvider(User::class, name: 'active')]
class ActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User { /* ... */ }
}

#[PhodamProvider(User::class, name: 'active')]
class AnotherActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User { /* ... */ }
}

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(ActiveUserProvider::class);

// This will throw ProviderConflictException
$schema->registerProvider(AnotherActiveUserProvider::class);

// This will work (overrides the existing provider)
#[PhodamProvider(User::class, name: 'active', overriding: true)]
class ImprovedActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User { /* ... */ }
}

$schema->registerProvider(ImprovedActiveUserProvider::class);
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
#[PhodamProvider(Order::class, name: 'pending')]
class PendingOrderProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): Order { /* ... */ }
}

#[PhodamProvider(Order::class, name: 'completed')]
class CompletedOrderProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): Order { /* ... */ }
}

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(PendingOrderProvider::class);
$schema->registerProvider(CompletedOrderProvider::class);

// In tests
$pendingOrder = $phodam->create(Order::class, 'pending');
$completedOrder = $phodam->create(Order::class, 'completed');
```

