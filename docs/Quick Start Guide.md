# Quick Start Guide

Welcome to Phodam! This guide will help you get started generating test objects in minutes.

## What is Phodam?

Phodam is a PHP library that automatically generates populated objects for your unit tests. Instead of manually creating test data, you can simply ask Phodam to create fully-populated objects with realistic test data.

```php
// Instead of this:
$student = new Student();
$student->setId(1);
$student->setName('John Doe');
$student->setGpa(3.5);
$student->setActive(true);
// ... and so on

// You can do this:
$student = $phodam->create(Student::class);
// ✅ Fully populated with random test data!
```

## Installation

Install Phodam using Composer:

```bash
composer require --dev phodam/phodam
```

**Note:** Phodam is installed as a dev dependency since it's used for testing.

## Basic Setup

### Step 1: Create a Phodam Instance

The easiest way to get started is using `PhodamSchema::withDefaults()`:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();
```

### Step 2: Use It in Your Tests

Here's a complete example in a PHPUnit test:

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
        // All fields are automatically populated!
    }
}
```

## Your First Examples

### Example 1: Creating Primitive Types

Phodam can generate primitive types out of the box:

```php
$int = $phodam->create('int');
$float = $phodam->create('float');
$string = $phodam->create('string');
$bool = $phodam->create('bool');
```

### Example 2: Creating Objects with Typed Properties

If your class has typed properties, Phodam can automatically populate it:

```php
class Student
{
    private int $id;
    private string $name;
    private float $gpa;
    private bool $active;
    private Address $address;
}

// Works automatically - no configuration needed!
$student = $phodam->create(Student::class);
```

### Example 3: Using Overrides

You can override specific fields when creating objects:

```php
$student = $phodam->create(Student::class, null, [
    'name' => 'John Doe',
    'gpa' => 4.0
]);

// $student->getName() will be 'John Doe'
// $student->getGpa() will be 4.0
// Other fields are still randomly generated
```

### Example 4: Configuring Primitive Types

You can configure how primitive types are generated:

```php
// Generate an age between 18 and 100
$age = $phodam->create('int', null, [], [
    'min' => 18,
    'max' => 100
]);

// Generate a GPA between 0.0 and 4.0 with 2 decimal places
$gpa = $phodam->create('float', null, [], [
    'min' => 0.0,
    'max' => 4.0,
    'precision' => 2
]);

// Generate an uppercase string
$username = $phodam->create('string', null, [], [
    'type' => 'upper',
    'minLength' => 10,
    'maxLength' => 20
]);
```

## Common Use Cases

### Use Case 1: Simple Object Generation

**Scenario:** You need a populated object for testing.

```php
class Product
{
    private int $id;
    private string $name;
    private float $price;
    private bool $inStock;
}

// Just works!
$product = $phodam->create(Product::class);
```

### Use Case 2: Objects with Specific Values

**Scenario:** You need an object with some specific values, but random data for the rest.

```php
// Create a student with a specific name and GPA
$student = $phodam->create(Student::class, null, [
    'name' => 'Jane Smith',
    'gpa' => 3.8
]);

// Other fields (id, active, address, etc.) are randomly generated
```

### Use Case 3: Nested Objects

**Scenario:** Your object contains other objects.

```php
class Order
{
    private int $orderId;
    private Customer $customer;  // Nested object
    private Address $shippingAddress;  // Nested object
}

// Phodam automatically creates nested objects too!
$order = $phodam->create(Order::class);
// $order->getCustomer() is automatically populated
// $order->getShippingAddress() is automatically populated
```

### Use Case 4: Arrays of Objects

**Scenario:** You need an array of objects.

```php
class Classroom
{
    /**
     * @var Student[]
     */
    private array $students;
}

$classroom = $phodam->create(Classroom::class);
// $classroom->getStudents() contains 2-5 Student objects
```

## Decision Tree: Which Approach Should I Use?

Not sure which approach to use? Follow this decision tree:

```
Do you have a class with typed properties?
│
├─ YES → Try automatic analysis first!
│         $phodam->create(MyClass::class)
│         If it works, you're done! ✅
│
└─ NO → Does it have PHPDoc @var annotations?
        │
        ├─ YES → Try automatic analysis!
        │         $phodam->create(MyClass::class)
        │         If it works, you're done! ✅
        │
        └─ NO → Do you need custom logic or defaults?
                │
                ├─ YES → Create a Custom Type Provider
                │         (See: Custom Type Providers guide)
                │
                └─ NO → Use a Type Definition
                          (See: Definition-based Type Providers guide)
```

### Quick Reference

| Situation | Solution |
|-----------|----------|
| Class has typed properties | Use automatic analysis: `$phodam->create(MyClass::class)` |
| Class has PHPDoc annotations | Use automatic analysis: `$phodam->create(MyClass::class)` |
| Need custom defaults/logic | Create a Custom Type Provider |
| Need to specify field types | Use a Type Definition |
| Need multiple variations | Use Named Providers |
| Need associative arrays | Create an Array Provider |

## Real-World Example

Let's see a complete example from start to finish:

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
        // Create a complete order with nested objects
        $order = $this->phodam->create(Order::class, null, [
            'status' => 'pending'
        ]);

        // The order has:
        // - Random orderId
        // - Fully populated Customer object
        // - Fully populated Address object
        // - Array of OrderItem objects
        // - status = 'pending' (from override)
        // - Random total, dates, etc.

        $service = new OrderService();
        $result = $service->processOrder($order);

        $this->assertEquals('processed', $result->getStatus());
    }

    public function testOrderWithSpecificCustomer(): void
    {
        // Create order with specific customer email
        $order = $this->phodam->create(Order::class, null, [
            'customer' => [
                'email' => 'test@example.com'
            ]
        ]);

        // Test with this specific customer
    }
}
```

## Tips for Success

### 1. Use Typed Properties When Possible

Phodam works best with typed properties (PHP 7.4+):

```php
// Good - Phodam can automatically detect types
class Product
{
    private int $id;
    private string $name;
    private float $price;
}

// Less ideal - requires PHPDoc or definitions
class Product
{
    private $id;
    private $name;
    private $price;
}
```

### 2. Add PHPDoc for Arrays

Always document array element types:

```php
// Good
/**
 * @var Student[]
 */
private array $students;

// Bad - Phodam can't determine element type
private array $students;
```

### 3. Use Overrides for Test-Specific Values

Don't create custom providers for one-off test cases:

```php
// Good - use overrides
$student = $phodam->create(Student::class, null, [
    'gpa' => 4.0
]);

// Overkill - creating a provider just for this
```

### 4. Set Up Phodam in Test Base Class

If you have multiple test classes, create a base class:

```php
abstract class TestCaseBase extends TestCase
{
    protected PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();
        $this->phodam = $schema->getPhodam();
    }
}

class MyTest extends TestCaseBase
{
    public function testSomething(): void
    {
        $object = $this->phodam->create(MyClass::class);
    }
}
```

## What's Next?

Now that you've got the basics, explore these topics:

1. **[Initializing Phodam](Initializing%20Phodam.md)** - Detailed setup instructions
2. **[Automatic Type Analysis](Automatic%20Type%20Analysis.md)** - How Phodam automatically analyzes your classes
3. **[Primitive Types](Primitive%20Types.md)** - Learn about configuring `int`, `float`, `string`, and `bool`
4. **[Built-in Types](Builtin%20Types.md)** - Working with `DateTime`, `DateTimeImmutable`, etc.
5. **[Associative Arrays](Associative%20Arrays.md)** - Creating structured arrays
6. **[Definition-based Type Providers](Definition-based%20Type%20Providers.md)** - When automatic analysis isn't enough
7. **[Custom Type Providers](Custom%20Type%20Providers.md)** - Full control over object creation
8. **[Named Providers](Named%20Providers.md)** - Multiple ways to generate the same type

## Common Questions

### Q: Do I need to register providers for every class?

**A:** No! If your class has typed properties or PHPDoc annotations, Phodam can automatically analyze and populate it. Only register providers when you need custom behavior.

### Q: Can I use Phodam with legacy code without type declarations?

**A:** Yes! Add PHPDoc `@var` annotations to your properties, and Phodam will use them. See the [Automatic Type Analysis](Automatic%20Type%20Analysis.md) and [Definition-based Type Providers](Definition-based%20Type%20Providers.md) guides for more details.

### Q: How do I handle circular references?

**A:** Use overrides to break the cycle, or create custom providers that handle the relationship explicitly.

### Q: Can I use Phodam with interfaces or abstract classes?

**A:** You'll need to register a provider that specifies which concrete class to use, as Phodam can't instantiate interfaces or abstract classes directly.

## Getting Help

- Check the other documentation guides for detailed information
- Review the examples in the `/examples` directory
- Look at the test files for usage patterns

## Summary

1. **Install:** `composer require --dev phodam/phodam`
2. **Setup:** `$phodam = PhodamSchema::withDefaults()->getPhodam();`
3. **Use:** `$object = $phodam->create(MyClass::class);`
4. **Customize:** Use overrides, config, or custom providers as needed

That's it! You're ready to start generating test objects. Happy testing! 🎉

