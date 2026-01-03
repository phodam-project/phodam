# Generating PHP 8 Enum Types with Phodam

This document describes how to generate PHP 8 enum values using Phodam. Enum support is automatically available when using `PhodamSchema::withDefaults()`.

## Overview

Phodam automatically detects and generates enum values for any PHP 8 enum type. The default enum provider returns a random case from the enum, making it perfect for generating test data.

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Generate enum values
$status = $phodam->create(OrderStatus::class);
$priority = $phodam->create(Priority::class);
$role = $phodam->create(UserRole::class);
```

**Note:** The enum provider automatically registers itself when you first request an enum type. No manual registration is required.

## Supported Enum Types

Phodam supports all PHP 8 enum types:

- **Pure Enums (UnitEnum)** - Enums without backing values
- **String-Backed Enums (BackedEnum)** - Enums with string values
- **Int-Backed Enums (BackedEnum)** - Enums with integer values

## Pure Enums (UnitEnum)

Pure enums are enums without backing values. They're perfect for representing a fixed set of states or options.

### Defining a Pure Enum

```php
enum OrderStatus
{
    case PENDING;
    case IN_PROGRESS;
    case COMPLETED;
    case CANCELLED;
}
```

### Generating a Pure Enum

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

$status = $phodam->create(OrderStatus::class);
// Returns: A random OrderStatus case (PENDING, IN_PROGRESS, COMPLETED, or CANCELLED)
```

### Example

```php
enum OrderStatus
{
    case PENDING;
    case IN_PROGRESS;
    case COMPLETED;
    case CANCELLED;
}

$status = $phodam->create(OrderStatus::class);
$this->assertInstanceOf(OrderStatus::class, $status);
$this->assertContains($status, OrderStatus::cases());
```

## String-Backed Enums

String-backed enums have string values associated with each case.

### Defining a String-Backed Enum

```php
enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
}
```

### Generating a String-Backed Enum

```php
$priority = $phodam->create(Priority::class);
// Returns: A random Priority case with its string value

// Access the backing value
echo $priority->value; // 'low', 'medium', 'high', or 'urgent'
```

### Example

```php
enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
}

$priority = $phodam->create(Priority::class);
$this->assertInstanceOf(Priority::class, $priority);
$this->assertIsString($priority->value);
$this->assertContains($priority->value, ['low', 'medium', 'high', 'urgent']);
```

## Int-Backed Enums

Int-backed enums have integer values associated with each case.

### Defining an Int-Backed Enum

```php
enum UserRole: int
{
    case GUEST = 1;
    case USER = 2;
    case MODERATOR = 3;
    case ADMIN = 4;
    case SUPER_ADMIN = 5;
}
```

### Generating an Int-Backed Enum

```php
$role = $phodam->create(UserRole::class);
// Returns: A random UserRole case with its integer value

// Access the backing value
echo $role->value; // 1, 2, 3, 4, or 5
```

### Example

```php
enum UserRole: int
{
    case GUEST = 1;
    case USER = 2;
    case MODERATOR = 3;
    case ADMIN = 4;
    case SUPER_ADMIN = 5;
}

$role = $phodam->create(UserRole::class);
$this->assertInstanceOf(UserRole::class, $role);
$this->assertIsInt($role->value);
$this->assertContains($role->value, [1, 2, 3, 4, 5]);
```

## Using Enums in Classes

Phodam automatically detects enum properties in your classes and uses the enum provider to populate them.

### Example Class with Enum Properties

```php
class Task
{
    private int $id;
    private string $title;
    private Priority $priority;      // Enum property
    private OrderStatus $status;     // Enum property
    private ?string $description;
}

// Phodam automatically detects and populates enum properties
$task = $phodam->create(Task::class);
$this->assertInstanceOf(Priority::class, $task->getPriority());
$this->assertInstanceOf(OrderStatus::class, $task->getStatus());
```

### Multiple Enums in One Class

```php
class Project
{
    private int $id;
    private string $name;
    private Priority $priority;      // String-backed enum
    private OrderStatus $status;     // Pure enum
    private UserRole $assignedRole;  // Int-backed enum
}

$project = $phodam->create(Project::class);
// All enum properties are automatically populated with random cases
```

## Overriding Enum Values

You can override enum values just like any other field:

```php
$task = $phodam->create(Task::class, null, [
    'priority' => Priority::HIGH,
    'status' => OrderStatus::IN_PROGRESS
]);

$this->assertEquals(Priority::HIGH, $task->getPriority());
$this->assertEquals(OrderStatus::IN_PROGRESS, $task->getStatus());
```

## Random Case Selection

The default enum provider returns a random case from the enum each time it's called. This is useful for generating varied test data:

```php
// Generate multiple enum values
$results = [];
for ($i = 0; $i < 10; $i++) {
    $results[] = $phodam->create(OrderStatus::class);
}

// All results are valid enum cases
foreach ($results as $result) {
    $this->assertInstanceOf(OrderStatus::class, $result);
    $this->assertContains($result, OrderStatus::cases());
}
```

## Automatic Registration

The enum provider is automatically registered when you first request an enum type. You don't need to manually register it:

```php
// First call - provider is automatically registered
$status1 = $phodam->create(OrderStatus::class);

// Subsequent calls use the registered provider
$status2 = $phodam->create(OrderStatus::class);
$status3 = $phodam->create(OrderStatus::class);
```

## Complete Example

Here's a complete example showing all enum types in action:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

// Define enums
enum OrderStatus
{
    case PENDING;
    case IN_PROGRESS;
    case COMPLETED;
    case CANCELLED;
}

enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
}

enum UserRole: int
{
    case GUEST = 1;
    case USER = 2;
    case MODERATOR = 3;
    case ADMIN = 4;
}

// Setup Phodam
$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Generate pure enum
$status = $phodam->create(OrderStatus::class);
echo $status->name; // PENDING, IN_PROGRESS, COMPLETED, or CANCELLED

// Generate string-backed enum
$priority = $phodam->create(Priority::class);
echo $priority->value; // 'low', 'medium', 'high', or 'urgent'

// Generate int-backed enum
$role = $phodam->create(UserRole::class);
echo $role->value; // 1, 2, 3, or 4

// Use in a class
class Task
{
    public function __construct(
        private int $id,
        private string $title,
        private Priority $priority,
        private OrderStatus $status
    ) {}
}

$task = $phodam->create(Task::class);
// All properties including enums are automatically populated
```

## Method Signature Reference

When calling `create()` for enum types:

```php
$value = $phodam->create(
    string $type,           // OrderStatus::class, Priority::class, etc.
    ?string $name = null,   // null for default provider, or a name for named providers
    ?array $overrides = null, // Can override enum values: ['status' => OrderStatus::COMPLETED]
    ?array $config = null    // Not currently used for enum provider
);
```

## Common Use Cases

### Use Case 1: Generating Test Data with Random States

```php
// Generate tasks with random priorities and statuses
$task = $phodam->create(Task::class);
// $task has random Priority and OrderStatus values
```

### Use Case 2: Testing Specific Enum Values

```php
// Override to test specific enum values
$task = $phodam->create(Task::class, null, [
    'priority' => Priority::URGENT,
    'status' => OrderStatus::IN_PROGRESS
]);
```

### Use Case 3: Generating Multiple Variations

```php
// Generate multiple tasks with different enum combinations
for ($i = 0; $i < 10; $i++) {
    $task = $phodam->create(Task::class);
    // Each task has random enum values
}
```

## Limitations

- **No Configuration Options**: The default enum provider doesn't support configuration options. It always returns a random case.
- **No Custom Selection Logic**: For custom selection logic (e.g., weighted random, exclude certain cases), create a custom provider.

## Creating Custom Enum Providers

If you need custom behavior (e.g., weighted random selection, excluding certain cases), you can create a custom provider:

```php
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

#[PhodamProvider(OrderStatus::class)]
class CustomOrderStatusProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): OrderStatus
    {
        // Custom logic - e.g., only return active statuses
        $activeStatuses = [
            OrderStatus::PENDING,
            OrderStatus::IN_PROGRESS
        ];
        
        return $activeStatuses[array_rand($activeStatuses)];
    }
}
```

## Summary

| Feature | Description |
|---------|-------------|
| **Pure Enums** | Automatically supported - returns random case |
| **String-Backed Enums** | Automatically supported - returns random case with string value |
| **Int-Backed Enums** | Automatically supported - returns random case with int value |
| **In Classes** | Automatically detected and populated |
| **Overrides** | Supported - can override enum values |
| **Registration** | Automatic - no manual registration needed |
| **Random Selection** | Always returns a random case from available cases |

Enum support is built into Phodam and works automatically with `PhodamSchema::withDefaults()`. Simply use `$phodam->create(YourEnum::class)` and Phodam will return a random enum case!
