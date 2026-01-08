# Custom Type Providers

Custom type providers give you complete control over object creation. They implement `TypedProviderInterface` and are ideal when you need custom logic, default values, or complex object construction that can't be handled by definition-based providers or automatic type analysis.

## When to Use Custom Providers

Use custom providers when you need:
- Custom default values (e.g., always active users, specific GPA ranges)
- Complex object construction logic
- State management (e.g., auto-incrementing IDs)
- Business rules that don't fit into simple field definitions

Use definition-based providers when you just need to specify field types, or automatic type analysis when your classes are well-typed.

## Creating a Custom Provider

Implement `TypedProviderInterface` with proper template annotations:

```php
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends Student
 * @template-implements TypedProviderInterface<Student>
 */
#[PhodamProvider(Student::class)]
class StudentTypeProvider implements TypedProviderInterface
{
    private int $id = 1;

    public function create(ProviderContextInterface $context): Student
    {
        $defaults = [
            'id' => $this->id++,
            'name' => $context->getPhodam()->create('string'),
            'gpa' => $context->getPhodam()->create('float', null, [], [
                'min' => 0.0,
                'max' => 4.0,
                'precision' => 2
            ]),
            'active' => true,
            'address' => $context->getPhodam()->create(Address::class),
            'dateOfBirth' => $context->getPhodam()->create(DateTimeImmutable::class)
        ];

        $values = array_merge($defaults, $context->getOverrides());

        return (new Student())
            ->setId($values['id'])
            ->setName($values['name'])
            ->setGpa($values['gpa'])
            ->setActive($values['active'])
            ->setAddress($values['address'])
            ->setDateOfBirth($values['dateOfBirth']);
    }
}
```

This example demonstrates custom defaults, state management, nested object creation, and override handling.

## Using ProviderContext

The `ProviderContextInterface` provides access to Phodam functionality:

- **`getPhodam()`**: Create nested objects and primitives
- **`getOverrides()`**: Access field value overrides
- **`getConfig()`**: Access provider-specific configuration

## Registering Providers

Register providers using the `#[PhodamProvider]` attribute and `registerProvider()`:

```php
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$schema->registerProvider(StudentTypeProvider::class);
$phodam = $schema->getPhodam();
```

### Default vs Named Providers

Register as a default provider (used when no name is specified):

```php
#[PhodamProvider(Student::class)]
class StudentTypeProvider implements TypedProviderInterface { /* ... */ }
```

Register as a named provider (requires specifying the name when creating):

```php
#[PhodamProvider(Student::class, name: 'activeStudent')]
class StudentTypeProvider implements TypedProviderInterface { /* ... */ }

// Usage
$student = $phodam->create(Student::class, name: 'activeStudent');
```

## Using Custom Providers

Once registered, use providers with overrides and configuration:

```php
// Basic usage
$student = $phodam->create(Student::class);

// With overrides
$student = $phodam->create(Student::class, overrides: [
    'name' => 'John Doe',
    'gpa' => 3.5
]);

// With config
$classroom = $phodam->create(Classroom::class, config: [
    'numStudents' => 20
]);
```

## Best Practices

1. **Always merge with overrides**: Use `array_merge($defaults, $context->getOverrides())` to allow customization
2. **Use context for nested objects**: Call `$context->getPhodam()->create()` instead of `new` to leverage Phodam's provider system
3. **Use config for behavior, overrides for values**: Config controls provider behavior; overrides set specific field values
4. **Maintain type safety**: Use proper return types and template annotations

## Summary

Custom type providers implement `TypedProviderInterface` and provide complete control over object creation. Use `ProviderContextInterface` to create nested objects, access overrides, and read configuration. Always merge defaults with overrides, and register providers using the `#[PhodamProvider]` attribute. Custom providers are ideal when you need custom logic, defaults, state management, or complex object construction that can't be handled by simpler approaches.
