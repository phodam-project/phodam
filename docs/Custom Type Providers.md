# Custom Type Providers

Custom type providers give you complete control over how objects are created. They are ideal when you need custom logic, default values, or complex object construction that can't be handled by definition-based providers or automatic type analysis.

## Overview

Custom type providers implement the `TypedProviderInterface` and provide full control over object instantiation. They are useful for:

- Setting custom default values (e.g., always active users, specific GPA ranges)
- Complex object construction logic
- Maintaining state (e.g., auto-incrementing IDs)
- Handling arrays with specific element types and counts
- Business logic that doesn't fit into simple field definitions

## Creating a Custom Type Provider

To create a custom type provider, implement the `TypedProviderInterface`:

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends MyClass
 * @template-implements TypedProviderInterface<MyClass>
 */
class MyClassTypeProvider implements TypedProviderInterface
{
    /**
     * @inheritDoc
     * @return MyClass
     */
    public function create(ProviderContext $context): MyClass
    {
        // Your custom creation logic here
        return new MyClass();
    }
}
```

### Key Components

- **Template Annotation**: Use PHPDoc `@template` to specify the generic type
- **Return Type**: The `create()` method should return the specific class type
- **ProviderContext**: Provides access to Phodam for creating nested objects and accessing overrides/config

## Using ProviderContext

The `ProviderContext` is your gateway to Phodam's functionality within a provider:

### Creating Nested Objects

Use `$context->create()` to create nested objects:

```php
public function create(ProviderContext $context): Student
{
    $address = $context->create(Address::class);
    $dateOfBirth = $context->create(DateTimeImmutable::class);
    
    // Use the created objects
    return new Student($address, $dateOfBirth);
}
```

### Creating Primitive Types with Configuration

You can create primitives with configuration:

```php
public function create(ProviderContext $context): Product
{
    $price = $context->create('float', null, [], [
        'min' => 0.01,
        'max' => 1000.0,
        'precision' => 2
    ]);
    
    return new Product($price);
}
```

### Handling Overrides

Always merge your defaults with overrides from the context:

```php
public function create(ProviderContext $context): User
{
    $defaults = [
        'name' => $context->create('string'),
        'email' => $context->create('string'),
        'active' => true
    ];
    
    // Merge with any overrides passed to create()
    $values = array_merge($defaults, $context->getOverrides());
    
    return new User(
        $values['name'],
        $values['email'],
        $values['active']
    );
}
```

### Using Config

Access configuration passed to `create()`:

```php
public function create(ProviderContext $context): Classroom
{
    $config = $context->getConfig();
    
    $numStudents = $config['numStudents'] ?? 10;
    
    // Use config to customize behavior
    // ...
}
```

## Complete Examples

### Example 1: Basic Custom Provider with Defaults

This example shows a provider that sets custom default values:

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;
use DateTimeImmutable;

/**
 * @template T extends Student
 * @template-implements TypedProviderInterface<Student>
 */
class StudentTypeProvider implements TypedProviderInterface
{
    /**
     * @inheritDoc
     * @return Student
     */
    public function create(ProviderContext $context): Student
    {
        $defaults = [
            'id' => 1,
            'name' => $context->create('string'),
            'gpa' => $context->create(
                'float',
                null,
                [],
                ['min' => 0.0, 'max' => 4.0, 'precision' => 2]
            ),
            'active' => true,  // Custom default: always active
            'address' => $context->create(Address::class),
            'dateOfBirth' => $context->create(DateTimeImmutable::class)
        ];

        // Merge with any overrides
        $values = array_merge($defaults, $context->getOverrides());

        return (new Student())
            ->setId((int) $values['id'])
            ->setName($values['name'])
            ->setGpa((float) $values['gpa'])
            ->setActive((bool) $values['active'])
            ->setAddress($values['address'])
            ->setDateOfBirth($values['dateOfBirth']);
    }
}
```

**Key Points:**
- Sets custom defaults (`active => true`, GPA range 0.0-4.0)
- Creates nested objects using `$context->create()`
- Merges defaults with overrides
- Returns a fully configured object

### Example 2: Provider with State (Auto-incrementing ID)

You can maintain state in your provider:

```php
/**
 * @template T extends Student
 * @template-implements TypedProviderInterface<Student>
 */
class StudentTypeProvider implements TypedProviderInterface
{
    private int $id = 1;

    /**
     * @inheritDoc
     * @return Student
     */
    public function create(ProviderContext $context): Student
    {
        $defaults = [
            'id' => $this->id++,  // Auto-incrementing ID
            'name' => $context->create('string'),
            // ... other fields
        ];

        $values = array_merge($defaults, $context->getOverrides());

        return (new Student())
            ->setId($values['id'])
            // ... set other fields
    }
}
```

**Note**: Provider instances are typically reused, so state is maintained across calls. If you need fresh state for each instance, create new provider instances.

### Example 3: Provider with Arrays

This example shows how to handle array fields:

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends Classroom
 * @template-implements TypedProviderInterface<Classroom>
 */
class ClassroomTypeProvider implements TypedProviderInterface
{
    /**
     * @inheritDoc
     * @return Classroom
     */
    public function create(ProviderContext $context): Classroom
    {
        $defaults = [
            'roomNumber' => $context->create(
                'int',
                null,
                [],
                ['min' => 100, 'max' => 499]
            )
        ];

        // Get numStudents from config or use default
        $config = $context->getConfig();
        $numStudents = $config['numStudents'] ?? 
            $context->create('int', null, [], ['min' => 10, 'max' => 15]);

        $values = array_merge($defaults, $context->getOverrides());

        $classroom = new Classroom();
        $classroom->setRoomNumber((int) $values['roomNumber']);

        // Create an array of Student objects
        // Since PHP doesn't support generic array types, we use array_map
        $students = array_map(
            fn ($i) => $context->create(Student::class),
            range(0, $numStudents - 1)
        );
        $classroom->setStudents($students);

        return $classroom;
    }
}
```

**Key Points:**
- Uses config to determine array size
- Creates multiple objects using `array_map` and `range`
- Handles arrays when PHP doesn't support generic types

### Example 4: Provider Using Config

This example shows how to use configuration:

```php
use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends Order
 * @template-implements TypedProviderInterface<Order>
 */
class OrderTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): Order
    {
        $config = $context->getConfig();
        
        // Use config to customize behavior
        $minItems = $config['minItems'] ?? 1;
        $maxItems = $config['maxItems'] ?? 5;
        $status = $config['status'] ?? 'pending';
        
        $numItems = $context->create('int', null, [], [
            'min' => $minItems,
            'max' => $maxItems
        ]);
        
        $items = array_map(
            fn ($i) => $context->create(OrderItem::class),
            range(0, $numItems - 1)
        );
        
        $defaults = [
            'items' => $items,
            'status' => $status,
            'total' => $context->create('float', null, [], [
                'min' => 0.01,
                'max' => 10000.0,
                'precision' => 2
            ])
        ];
        
        $values = array_merge($defaults, $context->getOverrides());
        
        return (new Order())
            ->setItems($values['items'])
            ->setStatus($values['status'])
            ->setTotal($values['total']);
    }
}
```

**Usage:**
```php
// Use with config
$order = $phodam->create(Order::class, null, [], [
    'minItems' => 3,
    'maxItems' => 10,
    'status' => 'completed'
]);
```

## Registering Custom Type Providers

### Registering as Default Provider

```php
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();

// Register as default provider
$schema->forType(Student::class)
    ->registerProvider(new StudentTypeProvider());

$phodam = $schema->getPhodam();

// Use it
$student = $phodam->create(Student::class);
```

### Registering as Named Provider

```php
$schema = PhodamSchema::withDefaults();

// Register as named provider
$schema->forType(Student::class)
    ->withName('activeStudent')
    ->registerProvider(new StudentTypeProvider());

$phodam = $schema->getPhodam();

// Use it
$student = $phodam->create(Student::class, 'activeStudent');
```

### Registering with Class String

You can register by class name string:

```php
$schema->forType(Student::class)
    ->registerProvider(StudentTypeProvider::class);
```

## Using Custom Providers

Once registered, use custom providers just like any other provider:

```php
// Basic usage
$student = $phodam->create(Student::class);

// With overrides
$student = $phodam->create(Student::class, null, [
    'name' => 'John Doe',
    'gpa' => 3.5
]);

// With config
$classroom = $phodam->create(Classroom::class, null, [], [
    'numStudents' => 20
]);

// With overrides and config
$order = $phodam->create(Order::class, null, [
    'status' => 'shipped'
], [
    'minItems' => 5
]);
```

## ProviderContext Methods Reference

| Method | Description | Example |
|--------|-------------|---------|
| `create(string $type, ?string $name, ?array $overrides, ?array $config)` | Create any type | `$context->create('string')` |
| `createArray(string $name, ?array $overrides, ?array $config)` | Create a named array | `$context->createArray('userProfile')` |
| `getOverrides()` | Get all overrides | `$context->getOverrides()` |
| `hasOverride(string $field)` | Check if field is overridden | `$context->hasOverride('name')` |
| `getOverride(string $field)` | Get specific override | `$context->getOverride('name')` |
| `getConfig()` | Get configuration | `$context->getConfig()` |
| `getType()` | Get the type being created | `$context->getType()` |

## Best Practices

### 1. Always Merge with Overrides

Always merge your defaults with overrides to allow customization:

```php
// Good
$values = array_merge($defaults, $context->getOverrides());

// Bad - ignores overrides
return new MyClass($defaults['field1'], $defaults['field2']);
```

### 2. Use Context for Nested Objects

Use `$context->create()` for nested objects rather than `new`:

```php
// Good - allows Phodam to handle nested objects
$address = $context->create(Address::class);

// Avoid - bypasses Phodam's provider system
$address = new Address();
```

### 3. Provide Sensible Defaults

Set defaults that make sense for your domain:

```php
// Good - realistic defaults
'active' => true,
'gpa' => $context->create('float', null, [], ['min' => 0.0, 'max' => 4.0])

// Avoid - unrealistic defaults
'active' => false,  // Most students should be active
'gpa' => 0.0        // Most students don't have 0.0 GPA
```

### 4. Use Config for Behavior, Overrides for Values

Use config for provider behavior, overrides for specific field values:

```php
// Config - controls provider behavior
$phodam->create(Classroom::class, null, [], ['numStudents' => 20]);

// Overrides - sets specific field values
$phodam->create(Student::class, null, ['name' => 'John Doe']);
```

### 5. Maintain Type Safety

Use proper return types and template annotations:

```php
/**
 * @template T extends Student
 * @template-implements TypedProviderInterface<Student>
 */
class StudentTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): Student
    {
        // ...
    }
}
```

## When to Use Custom Providers vs Definition-based Providers

**Use Custom Providers when:**
- You need custom logic or business rules
- You need to maintain state (e.g., auto-incrementing IDs)
- You need complex array handling
- You want to set domain-specific defaults
- You need conditional logic based on config

**Use Definition-based Providers when:**
- You just need to specify field types
- You want to leverage auto-completion for typed fields
- Simple field mapping is sufficient

## Summary

- Custom type providers implement `TypedProviderInterface`
- They provide complete control over object creation
- Use `ProviderContext` to create nested objects and access overrides/config
- Always merge defaults with overrides
- Register using `PhodamSchema::forType()->registerProvider()`
- Perfect for custom logic, defaults, and complex object construction

Custom type providers are the most powerful way to create objects in Phodam, giving you full control when you need it while still leveraging Phodam's features for nested objects and type generation.

