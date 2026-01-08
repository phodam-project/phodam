# Definition-based Type Providers

Definition-based type providers specify how to populate class fields using `FieldDefinition` and `TypeDefinition` objects. Use them when Phodam cannot automatically determine field types from type declarations or PHPDoc annotations.

## When to Use

Use definition-based providers for:

- **Untyped fields**: Properties without type declarations that Phodam cannot infer
- **Array fields**: Arrays without PHPDoc annotations specifying element types
- **Custom configuration**: Field-specific constraints (e.g., age ranges, GPA limits)
- **Partial definitions**: Define only problematic fields and let Phodam auto-complete the rest

Use automatic type analysis when classes have typed properties or PHPDoc annotations. Use custom providers when you need complex logic or state management.

## Creating a Type Definition

A `TypeDefinition` maps field names to `FieldDefinition` objects that specify how each field should be populated.

```php
use Phodam\PhodamSchema;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;

$schema = PhodamSchema::withDefaults();

$definition = new TypeDefinition(
    Student::class,
    fields: [
        'id' => new FieldDefinition('int'),
        'name' => new FieldDefinition('string'),
        'age' => new FieldDefinition('int', config: ['min' => 18, 'max' => 100]),
        'gpa' => new FieldDefinition('float', nullable: true, config: ['min' => 0.0, 'max' => 4.0, 'precision' => 2]),
        'tags' => new FieldDefinition('string', array: true),
        'address' => new FieldDefinition(Address::class)
    ]
);

$schema->registerTypeDefinition($definition);
$phodam = $schema->getPhodam();
$student = $phodam->create(Student::class);
```

## FieldDefinition Options

`FieldDefinition` supports several configuration methods:

| Method | Purpose | Example |
|--------|---------|---------|
| `FieldDefinition(string $type)` | Constructor - the type to generate | `new FieldDefinition('int')` |
| `setNullable(bool $nullable)` | Allow null values | `->setNullable(true)` |
| `setArray(bool $array)` | Generate an array of the type | `->setArray(true)` |
| `setName(?string $name)` | Use a named provider for this field | `->setName('activeUser')` |
| `setConfig(?array $config)` | Provider-specific configuration | `->setConfig(['min' => 0, 'max' => 100])` |

## Registering as a Named Provider

Register a definition as a named provider by providing a name in the constructor:

```php
$definition = new TypeDefinition(
    User::class,
    name: 'activeUser',
    fields: [
        'name' => new FieldDefinition('string'),
        'active' => new FieldDefinition('bool')
    ]
);

$schema->registerTypeDefinition($definition);
$user = $phodam->create(User::class, name: 'activeUser');
```

## Auto-completion

Phodam automatically completes fields not defined in your `TypeDefinition` using the `TypeAnalyzer`. Define only fields that cannot be automatically determined:

```php
class User
{
    private $id;        // Untyped - must define
    private $name;      // Untyped - must define
    private ?string $email;  // Typed - auto-detected
    private bool $active;     // Typed - auto-detected
}

// Only define untyped fields
$definition = new TypeDefinition(
    User::class,
    fields: [
        'id' => new FieldDefinition('int'),
        'name' => new FieldDefinition('string')
    ]
);
```

If Phodam cannot determine a field type and you haven't defined it, an `IncompleteDefinitionException` is thrown.

## Array Fields

When a field is marked as an array, Phodam generates an array containing 2-5 elements of the specified type:

```php
'items' => new FieldDefinition(OrderItem::class, array: true);

// Generates array with 2-5 OrderItem instances
$order = $phodam->create(Order::class);
count($order->getItems()); // Between 2 and 5
```

## Using Overrides

Override specific fields when creating instances:

```php
$student = $phodam->create(Student::class, overrides: ['name' => 'John Doe', 'age' => 30]);
```

## Best Practices

1. **Define only what's necessary**: Let Phodam auto-complete fields it can determine automatically
2. **Use configuration for constraints**: Apply constraints via `config` parameter rather than hardcoding values
3. **Be explicit for arrays**: Always specify `array: true` and the element type
4. **Use named providers for reuse**: Create named providers when the same field configuration is needed in multiple definitions

## Summary

Definition-based providers use `FieldDefinition` and `TypeDefinition` to specify field population when automatic type analysis fails. Register using `PhodamSchema::registerTypeDefinition()`. Only define fields that cannot be automatically determined - Phodam will auto-complete the rest. Support includes nullable fields, arrays, configuration, and named providers. Ideal for classes with untyped properties or when you need field-specific constraints.
