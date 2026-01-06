# Automatic Type Analysis

Phodam's `TypeAnalyzer` automatically analyzes classes and determines how to populate them without requiring manual type definitions or providers. This enables zero-configuration object generation for well-typed classes.

## Overview

When you call `$phodam->create(MyClass::class)` and no provider is registered, Phodam will:

1. Inspect the class using PHP's Reflection API
2. Analyze each property's type declaration
3. Extract type information from PHPDoc `@var` annotations when needed
4. Create and register a `TypeDefinition`

```php
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Automatic analysis occurs when no provider is registered
$student = $phodam->create(Student::class);
```

## What Can Be Detected

### Typed Properties

Properties with explicit type declarations are automatically detected. Use typed properties to eliminate the need for manual configuration.

```php
class Student
{
    private int $id;
    private string $name;
    private float $gpa;
    private ?string $email;
    private Address $address;
    private DateTimeImmutable $dateOfBirth;
}
```

### Array Types with PHPDoc

Array element types must be specified in PHPDoc annotations. Supported formats include `@var Student[]` and `@var array<Student>`.

```php
class Classroom
{
    /**
     * @var Student[]
     */
    private array $students;
}
```

### Untyped Properties with PHPDoc

Properties without type declarations can be analyzed using `@var` annotations. Note that types from PHPDoc only are automatically marked as nullable.

```php
class Product
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var string
     */
    private $name;
}
```

### Complex Types from PHPDoc

Class names can be extracted from PHPDoc, including fully qualified names. The analyzer resolves class names in the same namespace automatically.

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
    private $createdAt;
}
```

## What Cannot Be Detected

The analyzer throws a `TypeAnalysisException` when it encounters:

- Untyped properties without PHPDoc annotations
- Array types without element type information in PHPDoc
- Properties with no type information at all

```php
class Product
{
    private $id; // Error: No type information
    private array $items; // Error: No element type specified
}
```

**Solution:** Add PHPDoc annotations or provide a manual type definition.

## When to Use Automatic Analysis

Use automatic analysis when your classes have typed properties or can be annotated with PHPDoc. This approach minimizes configuration overhead and keeps your codebase maintainable.

Use manual type definitions when you need custom field configuration, have untyped properties without PHPDoc, or require named providers for nested objects.

Use custom providers when you need complex construction logic, state management, or complete control over object creation.

## Best Practices

1. **Prefer type declarations over PHPDoc** - Use PHP 7.4+ type declarations when possible
2. **Always document array element types** - Arrays require PHPDoc to specify element types
3. **Use fully qualified names for external classes** - Improves clarity and avoids namespace resolution issues
4. **Combine automatic analysis with partial definitions** - Only define fields that automatic analysis cannot handle

## Summary

Automatic type analysis enables zero-configuration object generation for classes with typed properties or PHPDoc annotations. Typed properties, nullable types, and PHPDoc-annotated arrays and untyped properties are automatically detected. When analysis fails, provide partial type definitions for unmapped fields. This feature reduces setup overhead while maintaining flexibility for complex scenarios.
