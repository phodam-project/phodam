# Generating Built-in Types with Phodam

This document describes how to generate PHP built-in date and time types using `PhodamInterface`. These types are automatically available when using `PhodamSchema::withDefaults()`.

## Overview

Built-in types are generated using the `PhodamInterface::create()` method with the class name:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use DateTime;
use DateTimeImmutable;
use DateInterval;
use DatePeriod;
use DateTimeZone;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Generate built-in types
$dateTime = $phodam->create(DateTime::class);
$dateTimeImmutable = $phodam->create(DateTimeImmutable::class);
$interval = $phodam->create(DateInterval::class);
$period = $phodam->create(DatePeriod::class);
$timezone = $phodam->create(DateTimeZone::class);
```

**Note:** Unlike primitive types, built-in types do not support configuration options. They are generated with sensible default values suitable for testing.

## DateTime

Generates a `DateTime` instance representing the current date and time.

### Basic Usage

```php
use DateTime;

$dateTime = $phodam->create(DateTime::class);
// Returns: A new DateTime instance with the current date and time
```

### Default Behavior

The default provider creates a `DateTime` object with no constructor arguments, which represents the current date and time:

```php
// Equivalent to:
$dateTime = new DateTime();
```

### Example

```php
use DateTime;

$dateTime = $phodam->create(DateTime::class);
// $dateTime is a DateTime instance representing now
echo $dateTime->format('Y-m-d H:i:s'); // Current date and time
```

## DateTimeImmutable

Generates a `DateTimeImmutable` instance representing the current date and time.

### Basic Usage

```php
use DateTimeImmutable;

$dateTimeImmutable = $phodam->create(DateTimeImmutable::class);
// Returns: A new DateTimeImmutable instance with the current date and time
```

### Default Behavior

The default provider creates a `DateTimeImmutable` object with no constructor arguments, which represents the current date and time:

```php
// Equivalent to:
$dateTimeImmutable = new DateTimeImmutable();
```

### Example

```php
use DateTimeImmutable;

$dateTimeImmutable = $phodam->create(DateTimeImmutable::class);
// $dateTimeImmutable is a DateTimeImmutable instance representing now
echo $dateTimeImmutable->format('Y-m-d H:i:s'); // Current date and time

// DateTimeImmutable instances are immutable
$newDate = $dateTimeImmutable->modify('+1 day'); // Returns a new instance
```

## DateInterval

Generates a `DateInterval` instance representing a 1-day interval.

### Basic Usage

```php
use DateInterval;

$interval = $phodam->create(DateInterval::class);
// Returns: A new DateInterval instance representing 1 day (P1D)
```

### Default Behavior

The default provider creates a `DateInterval` with the specification `'P1D'` (Period 1 Day):

```php
// Equivalent to:
$interval = new DateInterval('P1D');
```

### Example

```php
use DateInterval;
use DateTime;

$interval = $phodam->create(DateInterval::class);
// $interval represents 1 day

// Use it to add/subtract from a date
$date = new DateTime('2024-01-01');
$date->add($interval);
echo $date->format('Y-m-d'); // 2024-01-02

// Format the interval
echo $interval->format('%d days'); // 1 days
```

### Common Use Cases

While the default is a 1-day interval, you can create custom intervals using PHP's DateInterval constructor with different specifications:

- `'P1D'` - 1 day (default)
- `'P1W'` - 1 week
- `'P1M'` - 1 month
- `'P1Y'` - 1 year
- `'PT1H'` - 1 hour
- `'PT30M'` - 30 minutes

For custom intervals in your tests, consider creating a named provider or using overrides with a custom provider.

## DatePeriod

Generates a `DatePeriod` instance representing a period from the current date to 7 days in the future, with daily intervals.

### Basic Usage

```php
use DatePeriod;

$period = $phodam->create(DatePeriod::class);
// Returns: A new DatePeriod from now to +7 days with daily intervals
```

### Default Behavior

The default provider creates a `DatePeriod` with:
- **Start date**: Current date/time (`new DateTime()`)
- **Interval**: 1 day (`new DateInterval('P1D')`)
- **End date**: 7 days from now (`new DateTime('+7 days')`)

```php
// Equivalent to:
$start = new DateTime();
$interval = new DateInterval('P1D');
$end = new DateTime('+7 days');
$period = new DatePeriod($start, $interval, $end);
```

### Example

```php
use DatePeriod;

$period = $phodam->create(DatePeriod::class);
// $period is a DatePeriod from now to 7 days in the future

// Iterate over the period
foreach ($period as $date) {
    echo $date->format('Y-m-d') . "\n";
}
// Outputs 8 dates (start date + 7 more days)
```

### Use Cases

`DatePeriod` is useful for generating date ranges in tests:

```php
use DatePeriod;

$period = $phodam->create(DatePeriod::class);

// Check if a date falls within the period
$testDate = new DateTime('+3 days');
foreach ($period as $date) {
    if ($date->format('Y-m-d') === $testDate->format('Y-m-d')) {
        echo "Date is in period\n";
        break;
    }
}
```

## DateTimeZone

Generates a `DateTimeZone` instance representing UTC timezone.

### Basic Usage

```php
use DateTimeZone;

$timezone = $phodam->create(DateTimeZone::class);
// Returns: A new DateTimeZone instance for UTC
```

### Default Behavior

The default provider creates a `DateTimeZone` with the identifier `'UTC'`:

```php
// Equivalent to:
$timezone = new DateTimeZone('UTC');
```

### Example

```php
use DateTimeZone;
use DateTimeImmutable;

$timezone = $phodam->create(DateTimeZone::class);
// $timezone is a DateTimeZone instance for UTC

// Use it with DateTime objects
$date = new DateTimeImmutable('now', $timezone);
echo $date->getTimezone()->getName(); // UTC

// Get timezone information
echo $timezone->getName(); // UTC
```

### Use Cases

Common use cases for `DateTimeZone` in tests:

```php
use DateTimeZone;
use DateTimeImmutable;

$utc = $phodam->create(DateTimeZone::class);

// Create dates in UTC
$utcDate = new DateTimeImmutable('2024-01-01 12:00:00', $utc);

// Compare with other timezones
$newYork = new DateTimeZone('America/New_York');
$nyDate = $utcDate->setTimezone($newYork);
echo $nyDate->format('Y-m-d H:i:s T'); // Converted to New York time
```

## Complete Example

Here's a complete example showing all built-in types:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use DateTime;
use DateTimeImmutable;
use DateInterval;
use DatePeriod;
use DateTimeZone;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Generate all built-in types
$dateTime = $phodam->create(DateTime::class);
$dateTimeImmutable = $phodam->create(DateTimeImmutable::class);
$interval = $phodam->create(DateInterval::class);
$period = $phodam->create(DatePeriod::class);
$timezone = $phodam->create(DateTimeZone::class);

// Use them together
$date = new DateTimeImmutable('2024-01-01', $timezone);
$futureDate = $date->add($interval);
echo $futureDate->format('Y-m-d'); // 2024-01-02

// Iterate over a period
foreach ($period as $periodDate) {
    echo $periodDate->format('Y-m-d') . "\n";
}
```

## Method Signature Reference

When calling `create()` for built-in types:

```php
$value = $phodam->create(
    string $type,           // DateTime::class, DateTimeImmutable::class, etc.
    ?string $name = null,   // null for default provider, or a name for named providers
    ?array $overrides = null, // Not applicable for built-in types
    ?array $config = null    // Not supported for built-in types
);
```

## Summary

| Type | Default Value | Notes |
|------|---------------|-------|
| `DateTime` | Current date/time | `new DateTime()` |
| `DateTimeImmutable` | Current date/time | `new DateTimeImmutable()` |
| `DateInterval` | 1 day | `new DateInterval('P1D')` |
| `DatePeriod` | 7-day period from now | Daily intervals |
| `DateTimeZone` | UTC | `new DateTimeZone('UTC')` |

All built-in types are automatically available when using `PhodamSchema::withDefaults()` and require no configuration. For custom behavior, create named providers as shown in the examples above.

