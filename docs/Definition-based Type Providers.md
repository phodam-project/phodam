# Definition-based Type Providers

Definition-based type providers allow you to specify how to populate class fields using `FieldDefinition` and `TypeDefinition` objects. This is useful when working with classes that have untyped fields or when you need custom configuration for specific fields.

## Overview

Definition-based type providers are ideal for:

- Classes with untyped fields (properties without type declarations)
- Classes with array fields that need specific element types
- Fine-grained control over how individual fields are generated
- Classes that cannot be automatically analyzed by Phodam's `TypeAnalyzer`

## When to Use Definition-based Providers

Use definition-based providers when:

1. **Untyped Fields**: Your class has properties without type declarations that Phodam cannot automatically determine (through type or PHPDoc)
2. **Array Fields**: You need to specify the element type for array properties (and it doesn't have PHPDoc)
3. **Custom Configuration**: You need different configuration for specific fields (e.g., specific ranges for integers)
4. **Partial Definitions**: You want to define only problematic fields and let Phodam auto-complete the rest

## Creating a Type Definition

A `TypeDefinition` is a collection of field definitions. Each field is defined using a `FieldDefinition` object.

### Basic Type Definition

```php
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

$fields = [
    'myInt' => new FieldDefinition('int'),
    'myString' => new FieldDefinition('string'),
    'myBool' => new FieldDefinition('bool')
];

$definition = new TypeDefinition($fields);
```

### FieldDefinition Options

A `FieldDefinition` supports several options:

| Method | Description | Example |
|--------|-------------|---------|
| `FieldDefinition(string $type)` | Constructor - the type to generate | `new FieldDefinition('int')` |
| `setNullable(bool $nullable)` | Whether the field can be null | `->setNullable(true)` |
| `setArray(bool $array)` | Whether the field is an array | `->setArray(true)` |
| `setName(?string $name)` | Named provider to use for this field | `->setName('activeUser')` |
| `setConfig(?array $config)` | Configuration for the field's provider | `->setConfig(['min' => 0, 'max' => 100])` |
| `setOverrides(?array $overrides)` | Overrides for nested objects | `->setOverrides(['active' => true])` |

### Complete FieldDefinition Example

```php
use Phodam\Analyzer\FieldDefinition;

// Simple field
$intField = new FieldDefinition('int');

// Nullable field
$nullableField = (new FieldDefinition('float'))
    ->setNullable(true);

// Array field with element type
$arrayField = (new FieldDefinition(MyClass::class))
    ->setArray(true);

// Field with configuration
$configuredField = (new FieldDefinition('int'))
    ->setConfig(['min' => 18, 'max' => 100]);

// Field using a named provider
$namedProviderField = (new FieldDefinition(User::class))
    ->setName('activeUser');

// Field with all options
$complexField = (new FieldDefinition('float'))
    ->setNullable(true)
    ->setConfig(['min' => 0.0, 'max' => 4.0, 'precision' => 2]);
```

## Registering a Type Definition

You can register a type definition in two ways:

### Using `registerTypeDefinition()` on Phodam

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

$fields = [
    'myInt' => new FieldDefinition('int'),
    'myString' => new FieldDefinition('string')
];
$definition = new TypeDefinition($fields);

// Register the definition
$phodam->registerTypeDefinition(MyClass::class, $definition);

// Now you can create instances
$instance = $phodam->create(MyClass::class);
```

### Using `registerDefinition()` on PhodamSchema

You can also register definitions using the schema's fluent API:

```php
use Phodam\PhodamSchema;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

$schema = PhodamSchema::withDefaults();

$fields = [
    'myInt' => new FieldDefinition('int'),
    'myString' => new FieldDefinition('string')
];
$definition = new TypeDefinition($fields);

// Register using schema
$schema->forType(MyClass::class)
    ->registerDefinition($definition);

$phodam = $schema->getPhodam();
$instance = $phodam->create(MyClass::class);
```

### Registering as a Named Provider

You can register a definition as a named provider:

```php
$schema = PhodamSchema::withDefaults();

$definition = new TypeDefinition([
    'myInt' => new FieldDefinition('int'),
    'myString' => new FieldDefinition('string')
]);

$schema->forType(MyClass::class)
    ->withName('myCustomProvider')
    ->registerDefinition($definition);

$phodam = $schema->getPhodam();
$instance = $phodam->create(MyClass::class, 'myCustomProvider');
```

## Examples

### Example 1: Populating a Type with Untyped Fields

This is the most common use case - when your class has properties without type declarations:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

class User
{
    private $id;        // Untyped!
    private $name;      // Untyped!
    private ?string $email;
    private bool $active;
    
    // ... getters and setters
}

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Define only the untyped fields - Phodam will auto-complete the rest
$definition = new TypeDefinition([
    'id' => new FieldDefinition('int'),
    'name' => new FieldDefinition('string')
]);

$phodam->registerTypeDefinition(User::class, $definition);

// Now you can create User instances
$user = $phodam->create(User::class);
// $user->getId() will be an int
// $user->getName() will be a string
// $user->getEmail() will be a string (auto-detected from ?string type)
// $user->isActive() will be a bool (auto-detected from bool type)
```

**Note**: You only need to define fields that cannot be automatically determined. Phodam will attempt to auto-complete missing fields using the `TypeAnalyzer`.

### Example 2: Populating a Type with Array Fields

When you have an array field, you need to specify the element type:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

class Order
{
    private int $orderId;
    private array $items;  // What type are the items?
    
    // ... getters and setters
}

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Define the array field with its element type
$definition = new TypeDefinition([
    'items' => (new FieldDefinition(OrderItem::class))
        ->setArray(true)
]);

$phodam->registerTypeDefinition(Order::class, $definition);

$order = $phodam->create(Order::class);
// $order->getItems() will be an array of OrderItem instances (2-5 items by default)
```

### Example 3: Fields with Configuration

You can provide configuration for specific fields:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

class Student
{
    private $id;
    private $name;
    private $age;
    private $gpa;
    
    // ... getters and setters
}

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

$definition = new TypeDefinition([
    'id' => new FieldDefinition('int'),
    'name' => new FieldDefinition('string'),
    'age' => (new FieldDefinition('int'))
        ->setConfig(['min' => 18, 'max' => 100]),
    'gpa' => (new FieldDefinition('float'))
        ->setConfig(['min' => 0.0, 'max' => 4.0, 'precision' => 2])
]);

$phodam->registerTypeDefinition(Student::class, $definition);

$student = $phodam->create(Student::class);
// $student->getAge() will be between 18 and 100
// $student->getGpa() will be between 0.0 and 4.0 with 2 decimal places
```

### Example 4: Nullable Fields

You can mark fields as nullable:

```php
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

$definition = new TypeDefinition([
    'requiredField' => new FieldDefinition('string'),
    'optionalField' => (new FieldDefinition('string'))
        ->setNullable(true),
    'optionalInt' => (new FieldDefinition('int'))
        ->setNullable(true)
]);
```

### Example 5: Using Named Providers for Fields

You can use named providers for specific fields:

```php
use Phodam\PhodamSchema;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;

// First, register a named provider for User
$schema = PhodamSchema::withDefaults();
$schema->forType(User::class)
    ->withName('activeUser')
    ->registerProvider(new ActiveUserProvider());

// Then use it in a field definition
$definition = new TypeDefinition([
    'owner' => (new FieldDefinition(User::class))
        ->setName('activeUser'),
    'name' => new FieldDefinition('string')
]);

$schema->forType(Project::class)
    ->registerDefinition($definition);

$phodam = $schema->getPhodam();
$project = $phodam->create(Project::class);
// $project->getOwner() will be created using the 'activeUser' provider
```

### Example 6: Complete Example with Multiple Field Types

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;
use DateTimeImmutable;

class Product
{
    private $id;
    private string $name;
    private ?string $description;
    private array $tags;
    private float $price;
    private DateTimeImmutable $createdAt;
    private bool $inStock;
    
    // ... getters and setters
}

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

$definition = new TypeDefinition([
    // Untyped field
    'id' => new FieldDefinition('int'),
    
    // Array field with element type
    'tags' => (new FieldDefinition('string'))
        ->setArray(true),
    
    // Field with configuration
    'price' => (new FieldDefinition('float'))
        ->setConfig(['min' => 0.01, 'max' => 1000.0, 'precision' => 2]),
    
    // Built-in type (auto-detected, but can be explicit)
    'createdAt' => new FieldDefinition(DateTimeImmutable::class),
    
    // Field with configuration for string
    'name' => (new FieldDefinition('string'))
        ->setConfig(['minLength' => 5, 'maxLength' => 50])
]);

$phodam->registerTypeDefinition(Product::class, $definition);

$product = $phodam->create(Product::class);
// All fields will be properly populated according to their definitions
```

## Auto-completion of Definitions

If your type definition doesn't cover all fields, Phodam will attempt to auto-complete missing fields using the `TypeAnalyzer`. This is useful when you only want to define problematic fields:

```php
class MyClass
{
    private $untypedField;      // Must define this
    private string $typedField;  // Can be auto-detected
    private int $anotherTyped;   // Can be auto-detected
}

// Only define the untyped field
$definition = new TypeDefinition([
    'untypedField' => new FieldDefinition('string')
]);

// Phodam will auto-complete 'typedField' and 'anotherTyped'
$phodam->registerTypeDefinition(MyClass::class, $definition);
```

**Note**: If Phodam cannot determine a field type and you haven't defined it, an `IncompleteDefinitionException` will be thrown.

## Using Overrides

You can override specific fields when creating instances, just like with other providers:

```php
$user = $phodam->create(User::class, null, [
    'name' => 'John Doe',
    'age' => 30
]);
// The 'name' and 'age' fields will use the provided values instead of generated ones
```

## Array Field Behavior

When a field is marked as an array (`setArray(true)`), Phodam will generate an array containing 2-5 elements of the specified type:

```php
$definition = new TypeDefinition([
    'items' => (new FieldDefinition(OrderItem::class))
        ->setArray(true)
]);

// When creating an instance, items will be an array with 2-5 OrderItem objects
$order = $phodam->create(Order::class);
count($order->getItems()); // Will be between 2 and 5
```

## Best Practices

1. **Define Only What's Necessary**: Only define fields that cannot be automatically determined. Let Phodam auto-complete the rest.

2. **Use Configuration for Constraints**: Use `setConfig()` to apply constraints like min/max values rather than hardcoding values:

```php
// Good
->setConfig(['min' => 18, 'max' => 100])

// Avoid
// Hardcoding specific values
```

3. **Be Explicit for Arrays**: Always specify `setArray(true)` and the element type for array fields:

```php
// Good
(new FieldDefinition(Item::class))->setArray(true)

// Avoid
new FieldDefinition('array')  // Doesn't specify element type
```

4. **Use Named Providers for Reusability**: If you need the same field configuration in multiple definitions, consider creating a named provider:

```php
// Register once
$schema->forType(User::class)
    ->withName('activeUser')
    ->registerProvider(new ActiveUserProvider());

// Use in multiple definitions
->setName('activeUser')
```

5. **Leverage Nullable Types**: Use `setNullable(true)` for optional fields to match your domain model:

```php
// Good - matches the actual type
(new FieldDefinition('string'))->setNullable(true)  // For ?string
```

## Error Handling

### IncompleteDefinitionException

This exception is thrown when a field cannot be determined and is not defined:

```php
try {
    $phodam->create(MyClass::class);
} catch (IncompleteDefinitionException $e) {
    // Handle missing field definitions
    // $e->getMessage() will indicate which fields are missing
}
```

## Summary

- Definition-based providers use `FieldDefinition` and `TypeDefinition` to specify how fields should be populated
- Use them for classes with untyped fields or when you need custom configuration
- Register using `registerTypeDefinition()` on Phodam or `registerDefinition()` on PhodamSchema
- Only define fields that cannot be automatically determined - Phodam will auto-complete the rest
- Support for nullable fields, arrays, configuration, and named providers
- Perfect for classes with untyped properties or complex field requirements

Definition-based type providers give you fine-grained control over how objects are populated, making them ideal for classes that cannot be automatically analyzed by Phodam.

