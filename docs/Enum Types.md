# Generating PHP 8 Enum Types with Phodam

Phodam automatically detects and generates enum values for any PHP 8 enum type. The default enum provider returns a random case from the enum, making it ideal for generating varied test data. Enum support is automatically available when using `PhodamSchema::withDefaults()`.

## Overview

Enum generation requires no manual registration. When you first request an enum type, Phodam automatically registers the enum provider and returns a random case from the enum's available cases.

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

$status = $phodam->create(OrderStatus::class);
$priority = $phodam->create(Priority::class);
```

## Supported Enum Types

Phodam supports all PHP 8 enum types:
- **Pure Enums (UnitEnum)** - Enums without backing values
- **String-Backed Enums (BackedEnum)** - Enums with string values
- **Int-Backed Enums (BackedEnum)** - Enums with integer values

## Pure Enums

Pure enums represent a fixed set of states or options without backing values. Use them when you need type-safe constants that don't require associated values.

```php
enum OrderStatus
{
    case PENDING;
    case IN_PROGRESS;
    case COMPLETED;
    case CANCELLED;
}

$status = $phodam->create(OrderStatus::class);
// Returns: A random OrderStatus case (PENDING, IN_PROGRESS, COMPLETED, or CANCELLED)
```

## String-Backed Enums

String-backed enums have string values associated with each case. Use them when you need enum values that can be serialized to strings or stored in databases.

```php
enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';
}

$priority = $phodam->create(Priority::class);
// Returns: A random Priority case with its string value
echo $priority->value; // 'low', 'medium', 'high', or 'urgent'
```

## Int-Backed Enums

Int-backed enums have integer values associated with each case. Use them when you need enum values that map to integer identifiers, such as database primary keys or numeric status codes.

```php
enum UserRole: int
{
    case GUEST = 1;
    case USER = 2;
    case MODERATOR = 3;
    case ADMIN = 4;
}

$role = $phodam->create(UserRole::class);
// Returns: A random UserRole case with its integer value
echo $role->value; // 1, 2, 3, or 4
```

## Using Enums in Classes

Phodam automatically detects enum properties in your classes and uses the enum provider to populate them. This enables generating complex objects with enum fields without manual configuration.

```php
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
// All properties including enums are automatically populated with random cases
```

## Overriding Enum Values

You can override enum values just like any other field when creating objects. This is useful for testing specific enum combinations or ensuring certain states in your test data.

```php
$task = $phodam->create(Task::class, overrides: [
    'priority' => Priority::HIGH,
    'status' => OrderStatus::IN_PROGRESS
]);
```

## Custom Enum Providers

The default enum provider always returns a random case. If you need custom behavior (e.g., weighted random selection, excluding certain cases, or always returning specific cases), create a custom provider.

```php
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

#[PhodamProvider(OrderStatus::class, name: 'active')]
class ActiveOrderStatusProvider implements TypedProviderInterface
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

Phodam provides automatic enum generation for all PHP 8 enum types. Pure enums, string-backed enums, and int-backed enums are all supported without manual registration. Enum properties in classes are automatically detected and populated. The default provider returns a random case from the enum, which is ideal for generating varied test data. For custom selection logic, create a custom provider implementing `TypedProviderInterface`.
