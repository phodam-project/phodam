# Automatic Type Analysis

Phodam includes a powerful `TypeAnalyzer` that can automatically analyze your classes and determine how to populate them, without requiring you to manually create type definitions or providers. This guide explains how automatic type analysis works, what it can detect, and when you need to provide additional information.

## Overview

When you call `$phodam->create(MyClass::class)` and no provider is registered for that type, Phodam automatically uses the `TypeAnalyzer` to:

1. Inspect the class using PHP's Reflection API
2. Analyze each property's type declaration
3. Extract type information from PHPDoc `@var` annotations when needed
4. Create a `TypeDefinition` that tells Phodam how to populate the object
5. Automatically register this definition as a provider

This means that for many classes, you can start using Phodam immediately without any setup!

## When Automatic Analysis is Triggered

Automatic type analysis is triggered automatically when:

1. You call `$phodam->create(MyClass::class)` 
2. No provider is registered for `MyClass`
3. Phodam catches a `ProviderNotFoundException`

At this point, Phodam automatically calls `TypeAnalyzer::analyze()` to create a type definition on-the-fly.

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// No provider registered - automatic analysis kicks in!
$student = $phodam->create(Student::class);
```

## What TypeAnalyzer Can Detect

The `TypeAnalyzer` can automatically detect and handle the following:

### 1. Typed Properties (PHP 7.4+)

Properties with explicit type declarations are automatically detected:

```php
class Student
{
    private int $id;                    // ✅ Detected as 'int'
    private string $name;               // ✅ Detected as 'string'
    private float $gpa;                  // ✅ Detected as 'float'
    private bool $active;                // ✅ Detected as 'bool'
    private ?string $email;               // ✅ Detected as 'string', nullable
    private Address $address;            // ✅ Detected as Address class
    private DateTimeImmutable $dateOfBirth; // ✅ Detected as DateTimeImmutable
}
```

**Result:** All fields are automatically mapped. No additional configuration needed!

### 2. Nullable Types

The analyzer correctly identifies nullable types:

```php
class User
{
    private string $name;        // ✅ Required (not nullable)
    private ?string $email;      // ✅ Optional (nullable)
    private ?int $age;           // ✅ Optional (nullable)
}
```

**Result:** Nullable fields are marked as nullable, and Phodam may generate `null` values for them.

### 3. Array Types with PHPDoc

For array properties, the analyzer can extract the element type from PHPDoc:

```php
class Classroom
{
    /**
     * @var Student[]
     */
    private array $students;  // ✅ Detected as array of Student objects
}
```

**Supported PHPDoc formats:**
- `@var Student[]` - Array of Student objects
- `@var string[]` - Array of strings
- `@var int[]` - Array of integers
- `@var array<Student>` - Alternative syntax (also supported)

**Result:** The array is automatically populated with 2-5 elements of the specified type.

### 4. Untyped Properties with PHPDoc

Properties without type declarations can be analyzed if they have `@var` annotations:

```php
class Product
{
    /**
     * @var int
     */
    private $id;              // ✅ Detected from @var annotation
    
    /**
     * @var string
     */
    private $name;            // ✅ Detected from @var annotation
    
    /**
     * @var float
     */
    private $price;           // ✅ Detected from @var annotation
}
```

**Important:** When a type comes from PHPDoc only (no type declaration), it's automatically marked as nullable by default.

**Result:** All fields are mapped based on PHPDoc annotations.

### 5. Complex Types from PHPDoc

The analyzer can extract class names from PHPDoc:

```php
class Order
{
    /**
     * @var Address
     */
    private $shippingAddress;  // ✅ Detected as Address class
    
    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;        // ✅ Fully qualified class names work
}
```

**Result:** Nested objects are automatically created.

## What TypeAnalyzer Cannot Detect

The analyzer will throw a `TypeAnalysisException` if it encounters:

### 1. Untyped Properties Without PHPDoc

```php
class Product
{
    private $id;        // ❌ No type declaration, no @var annotation
    private $name;      // ❌ No type declaration, no @var annotation
}
```

**Error:** `TypeAnalysisException: Product: Unable to map fields: id, name`

**Solution:** Add PHPDoc annotations or use a type definition (see below).

### 2. Array Types Without Element Type Information

```php
class Order
{
    private array $items;  // ❌ No PHPDoc indicating element type
}
```

**Error:** `TypeAnalysisException: Order: Unable to map fields: items`

**Solution:** Add PHPDoc: `/** @var OrderItem[] */ private array $items;`

### 3. Properties Without Any Type Information

If a property has neither a type declaration nor a PHPDoc `@var` annotation, it cannot be analyzed:

```php
class User
{
    private $unknownField;  // ❌ No type information at all
}
```

**Error:** `TypeAnalysisException: User: Unable to map fields: unknownField`

**Solution:** Add a type declaration or PHPDoc annotation.

## How to Help TypeAnalyzer with PHPDoc

When you have untyped properties or need to specify array element types, use PHPDoc `@var` annotations:

### Basic PHPDoc Format

```php
class MyClass
{
    /**
     * @var int
     */
    private $myInt;
    
    /**
     * @var string
     */
    private $myString;
}
```

### Array Types in PHPDoc

```php
class Classroom
{
    /**
     * @var Student[]
     */
    private array $students;
    
    /**
     * @var string[]
     */
    private array $tags;
    
    /**
     * @var array<OrderItem>
     */
    private array $items;  // Alternative syntax
}
```

### Nullable Types in PHPDoc

```php
class User
{
    /**
     * @var string|null
     */
    private $email;
    
    /**
     * @var int|null
     */
    private $age;
}
```

### Class Types in PHPDoc

```php
class Order
{
    /**
     * @var Address
     */
    private $shippingAddress;
    
    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;  // Fully qualified names work
}
```

### Namespace Resolution

The analyzer attempts to resolve class names in the same namespace:

```php
namespace App\Models;

class Order
{
    /**
     * @var Address  // Resolved as App\Models\Address
     */
    private $address;
    
    /**
     * @var \App\ValueObjects\Money  // Fully qualified
     */
    private $total;
}
```

## Examples

### Example 1: Fully Typed Class (No Configuration Needed)

```php
class Student
{
    private int $id;
    private string $name;
    private float $gpa;
    private bool $active;
    private Address $address;
    private DateTimeImmutable $dateOfBirth;
    
    // ... getters and setters
}

// Works automatically - no setup needed!
$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

$student = $phodam->create(Student::class);
// ✅ All fields automatically populated
```

### Example 2: Mixed Typed and Untyped Properties

```php
class Product
{
    private int $id;  // ✅ Typed - automatically detected
    
    /**
     * @var string
     */
    private $name;    // ✅ PHPDoc - automatically detected
    
    private float $price;  // ✅ Typed - automatically detected
    
    /**
     * @var string[]
     */
    private array $tags;  // ✅ Array with PHPDoc - automatically detected
}

// Works automatically!
$product = $phodam->create(Product::class);
// ✅ All fields automatically populated
```

### Example 3: Class That Needs Help

```php
class Order
{
    private int $orderId;  // ✅ Typed
    
    private array $items;  // ❌ No element type information
    
    /**
     * @var OrderItem[]
     */
    private array $items;  // ✅ Fixed with PHPDoc!
}
```

### Example 4: Partial Type Information

If some fields can't be analyzed, you can provide a partial definition:

```php
class User
{
    private string $name;  // ✅ Automatically detected
    
    private $legacyField;  // ❌ No type information
}

// usage
    // Provide definition only for the problematic field
    $schema = PhodamSchema::withDefaults();
    $definition = new TypeDefinition(
        User::class,
        null,
        false,
        [
            'legacyField' => new FieldDefinition('string')
        ]
    );
    
    $schema->registerTypeDefinition($definition);
    $phodam = $schema->getPhodam();
    
    // Now it works!
    $user = $phodam->create(User::class);
```

## When to Use Automatic Analysis vs Manual Definitions

### Use Automatic Analysis When:

✅ Your class has typed properties (PHP 7.4+)  
✅ You can add PHPDoc annotations for untyped properties  
✅ Your arrays have PHPDoc element type information  
✅ You want zero-configuration object generation  

**Example:**
```php
// Just works - no setup needed!
$student = $phodam->create(Student::class);
```

### Use Type Definitions When:

✅ You have untyped properties without PHPDoc (and can't add them)  
✅ You need custom configuration for specific fields (e.g., min/max values)  
✅ You want to use named providers for nested objects  
✅ You need to override automatic analysis behavior  

**Example:**
```php
$schema = PhodamSchema::withDefaults();
$definition = new TypeDefinition(
    Student::class,
    null,
    false,
    [
        'gpa' => (new FieldDefinition('float'))
            ->setConfig(['min' => 0.0, 'max' => 4.0, 'precision' => 2])
    ]
);

$schema->registerTypeDefinition($definition);
$phodam = $schema->getPhodam();
```

### Use Custom Providers When:

✅ You need complex object construction logic  
✅ You need to maintain state (e.g., auto-incrementing IDs)  
✅ You need business logic that doesn't fit into simple definitions  
✅ You want complete control over object creation  

## How Automatic Analysis Works Internally

1. **Reflection**: Uses PHP's `ReflectionClass` to inspect the class
2. **Property Analysis**: For each property:
   - Checks for type declaration (`ReflectionProperty::getType()`)
   - If no type, checks for PHPDoc `@var` annotation
   - For arrays, extracts element type from PHPDoc
   - Determines nullability
3. **TypeDefinition Creation**: Creates a `TypeDefinition` with `FieldDefinition` objects
4. **Auto-Registration**: Registers the definition as a provider automatically

## Error Handling

If automatic analysis fails, you'll get a `TypeAnalysisException`:

```php
try {
    $user = $phodam->create(User::class);
} catch (TypeAnalysisException $e) {
    // Get information about what failed
    $unmappedFields = $e->getUnmappedFields();
    // ['unknownField1', 'unknownField2']
    
    $mappedFields = $e->getMappedFields();
    // Fields that were successfully mapped
    
    // Provide definitions for unmapped fields
    $schema = PhodamSchema::withDefaults();
    $definition = new TypeDefinition(
        User::class,
        null,
        false,
        [
            'unknownField1' => new FieldDefinition('string'),
            'unknownField2' => new FieldDefinition('int')
        ]
    );
    
    $schema->registerTypeDefinition($definition);
    $phodam = $schema->getPhodam();
    
    // Try again
    $user = $phodam->create(User::class);
}
```

## Best Practices

### 1. Prefer Type Declarations Over PHPDoc

When possible, use PHP 7.4+ type declarations:

```php
// Good - type declaration
private int $id;

// Also works, but less ideal
/**
 * @var int
 */
private $id;
```

### 2. Always Document Array Element Types

```php
// Good
/**
 * @var Student[]
 */
private array $students;

// Bad - analyzer can't determine element type
private array $students;
```

### 3. Use Fully Qualified Names for External Classes

```php
// Good - explicit
/**
 * @var \DateTimeImmutable
 */
private $createdAt;

// Also works if in same namespace
/**
 * @var Address
 */
private $address;
```

### 4. Combine Automatic Analysis with Partial Definitions

You don't need to define everything - just the problematic fields:

```php
// Only define what automatic analysis can't handle
$schema = PhodamSchema::withDefaults();
$definition = new TypeDefinition(
    MyClass::class,
    null,
    false,
    [
        'legacyField' => new FieldDefinition('string')
    ]
);

$schema->registerTypeDefinition($definition);
$phodam = $schema->getPhodam();
// Other fields are still auto-analyzed!
```

## Summary

- **Automatic type analysis** works when no provider is registered
- **Typed properties** are automatically detected
- **PHPDoc `@var` annotations** can provide type information for untyped properties
- **Array element types** must be specified in PHPDoc
- **Nullable types** are automatically detected
- **Partial definitions** can fill in gaps when automatic analysis fails
- **Zero configuration** is possible for well-typed classes

Automatic type analysis makes Phodam easy to use - just add type declarations or PHPDoc annotations to your classes, and Phodam will figure out the rest!

