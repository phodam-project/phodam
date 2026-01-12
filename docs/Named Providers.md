# Named Providers

Named providers allow you to register multiple providers for the same type, each identified by a unique name. This enables different generation strategies for the same class.

## When to Use Named Providers

Use named providers when you need:
- **Multiple generation strategies**: Different ways to create the same type (e.g., active vs inactive users)
- **Array providers**: Arrays must use named providers - there are no default array providers

Use default providers when a single generation strategy is sufficient for a type.

## Registering Named Type Providers

Register a named provider using the `#[PhodamProvider]` attribute with a `name` parameter:

```php
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
            true                                      // active = true
        );
    }
}

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(ActiveUserProvider::class);
$phodam = $schema->getPhodam();
```

## Registering Named Array Providers

Array providers **must** be named. Use the `#[PhodamArrayProvider]` attribute:

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
            'age' => $context->getPhodam()->create('int', null, [], ['min' => 18, 'max' => 100])
        ];

        return array_merge($defaults, $context->getOverrides());
    }
}

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(UserProfileArrayProvider::class);
$phodam = $schema->getPhodam();
```

## Using Named Providers

### Type Providers

Pass the provider name using the `name` parameter to `create()`:

```php
// Default provider (no name)
$defaultUser = $phodam->create(User::class);

// Named provider
$activeUser = $phodam->create(User::class, name: 'active');

// With overrides and config
$activeUser = $phodam->create(
    User::class,
    name: 'active',
    overrides: ['name' => 'John'],
    config: ['role' => 'admin']
);
```

### Array Providers

Use `createArray()` with the provider name:

```php
$profile = $phodam->createArray('userProfile');

// With overrides
$profile = $phodam->createArray('userProfile', ['email' => 'custom@example.com']);

// With overrides and config
$profile = $phodam->createArray('userProfile', ['email' => 'custom@example.com'], ['minAge' => 18]);
```

## Default vs Named Providers

You can register both default and named providers for the same type:

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

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(DefaultUserProvider::class);
$schema->registerProvider(AdminUserProvider::class);

// Use default provider
$user = $phodam->create(User::class);

// Use named provider
$admin = $phodam->create(User::class, 'admin');
```

## Overriding Providers

Replace an existing provider (default or named) using the `overriding` parameter:

```php
#[PhodamProvider(User::class, name: 'active', overriding: true)]
class ImprovedActiveUserProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): User { /* ... */ }
}

$schema->registerProvider(ImprovedActiveUserProvider::class);  // Replaces existing 'active' provider
```

Without `overriding: true`, registering a provider with an existing name throws `ProviderConflictException`.

## Error Handling

- **`ProviderNotFoundException`**: Thrown when using a named provider that doesn't exist
- **`ProviderConflictException`**: Thrown when registering a provider with an existing name (unless `overriding: true`)

## Best Practices

1. **Use descriptive names**: Choose names that clearly indicate purpose (e.g., `'activeUser'` not `'user1'`)
2. **Arrays must be named**: Remember that array providers require a name
3. **Combine with overrides**: Named providers work seamlessly with overrides and config parameters
4. **Use for test scenarios**: Create distinct providers for different test cases (pending vs completed orders)

## Summary

Named providers enable multiple generation strategies for the same type, identified by unique names. Register type providers with `#[PhodamProvider(type, name: 'name')]` and array providers with `#[PhodamArrayProvider('name')]`. Arrays must always use named providers. Use named providers when you need different generation strategies, test scenarios, or context-specific instances. You can register both default and named providers for the same type, and use the `overriding` parameter to replace existing providers.
