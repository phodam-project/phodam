# Quick Start Guide

Phodam is a PHP library that automatically generates populated objects for unit tests, eliminating the need to manually create test data.

## What is Phodam?

Phodam automatically generates fully-populated objects with realistic test data. This reduces boilerplate code in test setup and ensures consistent test data generation across your test suite.

```php
// Instead of manually setting each property:
$student = new Student();
$student->setId(1);
$student->setName('John Doe');
$student->setGpa(3.5);
$student->setActive(true);

// Phodam generates a fully-populated object:
$student = $phodam->create(Student::class);
```

## Installation

Install Phodam as a development dependency using Composer:

```bash
composer require --dev phodam/phodam
```

## Basic Setup

### Step 1: Create a Phodam Instance

Initialize Phodam using `PhodamSchema::withDefaults()` to get started with sensible defaults:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();
```

### Step 2: Use It in Your Tests

Integrate Phodam into your PHPUnit test classes:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class StudentServiceTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();
        $this->phodam = $schema->getPhodam();
    }

    public function testStudentCreation(): void
    {
        $student = $this->phodam->create(Student::class);
        
        $this->assertInstanceOf(Student::class, $student);
        $this->assertIsInt($student->getId());
        $this->assertIsString($student->getName());
    }
}
```

## Core Functionality

### Creating Objects with Typed Properties

Phodam automatically analyzes classes with typed properties and generates appropriate values. This eliminates the need for manual configuration when working with modern PHP code.

```php
class Student
{
    private int $id;
    private string $name;
    private float $gpa;
    private bool $active;
    private Address $address;
}

$student = $phodam->create(Student::class);
```

### Using Overrides

Override specific fields when you need test-specific values while allowing Phodam to generate the remaining properties. This approach maintains test isolation while reducing setup code.

```php
$student = $phodam->create(Student::class, null, [
    'name' => 'John Doe',
    'gpa' => 4.0
]);
```

### Configuring Primitive Types

Configure primitive type generation to match your domain constraints, ensuring generated values are realistic for your test scenarios.

```php
$age = $phodam->create('int', null, [], [
    'min' => 18,
    'max' => 100
]);
```

## Common Use Cases

### Nested Objects

Phodam automatically handles object composition, generating nested objects recursively. This simplifies testing of complex object graphs without manual construction.

```php
class Order
{
    private int $orderId;
    private Customer $customer;
    private Address $shippingAddress;
}

$order = $phodam->create(Order::class);
```

## Decision Tree

```
Do you have a class with typed properties?
│
├─ YES → Use automatic analysis: $phodam->create(MyClass::class)
│
└─ NO → Does it have PHPDoc @var annotations?
        │
        ├─ YES → Use automatic analysis: $phodam->create(MyClass::class)
        │
        └─ NO → Do you need custom logic or defaults?
                │
                ├─ YES → Create a Custom Type Provider
                │
                └─ NO → Use a Type Definition
```

## Real-World Example

This example demonstrates Phodam in a typical service test scenario:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();
        $this->phodam = $schema->getPhodam();
    }

    public function testProcessOrder(): void
    {
        $order = $this->phodam->create(Order::class, null, [
            'status' => 'pending'
        ]);

        $service = new OrderService();
        $result = $service->processOrder($order);

        $this->assertEquals('processed', $result->getStatus());
    }
}
```

## Best Practices

### Use Typed Properties

Typed properties enable automatic type detection, reducing configuration overhead. Phodam works best with PHP 7.4+ typed properties.

```php
class Product
{
    private int $id;
    private string $name;
    private float $price;
}
```

### Document Array Element Types

Always document array element types using PHPDoc annotations to enable automatic array generation.

```php
/**
 * @var Student[]
 */
private array $students;
```

### Use Overrides for Test-Specific Values

Prefer overrides over custom providers for one-off test cases to maintain simplicity and reduce maintenance burden.

```php
$student = $phodam->create(Student::class, null, [
    'gpa' => 4.0
]);
```

## What's Next?

For detailed information on specific topics, refer to these guides:

1. **[Initializing Phodam](Initializing%20Phodam.md)** - Detailed setup instructions
2. **[Automatic Type Analysis](Automatic%20Type%20Analysis.md)** - How Phodam analyzes classes
3. **[Primitive Types](Primitive%20Types.md)** - Configuring primitive type generation
4. **[Built-in Types](Builtin%20Types.md)** - Working with DateTime and other built-in types
5. **[Enum Types](Enum%20Types.md)** - Generating PHP 8 enum values
6. **[Associative Arrays](Associative%20Arrays.md)** - Creating structured arrays
7. **[Definition-based Type Providers](Definition-based%20Type%20Providers.md)** - When automatic analysis isn't sufficient
8. **[Custom Type Providers](Custom%20Type%20Providers.md)** - Full control over object creation
9. **[Named Providers](Named%20Providers.md)** - Multiple generation strategies for the same type

## Common Questions

### Do I need to register providers for every class?

No. If your class has typed properties or PHPDoc annotations, Phodam can automatically analyze and populate it. Only register providers when you need custom behavior.

### Can I use Phodam with legacy code without type declarations?

Yes. Add PHPDoc `@var` annotations to your properties, and Phodam will use them. See the [Automatic Type Analysis](Automatic%20Type%20Analysis.md) and [Definition-based Type Providers](Definition-based%20Type%20Providers.md) guides for details.

### How do I handle circular references?

Use overrides to break the cycle, or create custom providers that handle the relationship explicitly.

### Can I use Phodam with interfaces or abstract classes?

You must register a provider that specifies which concrete class to use, as Phodam cannot instantiate interfaces or abstract classes directly.

## Summary

1. **Install:** `composer require --dev phodam/phodam`
2. **Setup:** `$schema = PhodamSchema::withDefaults(); $phodam = $schema->getPhodam();`
3. **Use:** `$object = $phodam->create(MyClass::class);`
4. **Customize:** Use overrides, configuration, or custom providers as needed
