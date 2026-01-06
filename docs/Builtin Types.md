# Generating Built-in Types with Phodam

This document describes how to generate PHP built-in date and time types using `PhodamInterface`. These types are automatically available when using `PhodamSchema::withDefaults()`.

## Overview

Built-in types are generated using the `PhodamInterface::create()` method with the class name. Unlike primitive types, built-in types do not support configuration options and are generated with sensible default values suitable for testing.

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

$dateTime = $phodam->create(DateTime::class);
$dateTimeImmutable = $phodam->create(DateTimeImmutable::class);
$interval = $phodam->create(DateInterval::class);
$period = $phodam->create(DatePeriod::class);
$timezone = $phodam->create(DateTimeZone::class);
```

## DateTime

Generates a `DateTime` instance representing the current date and time. Use this when you need a mutable date object for testing date manipulation operations or when your code requires `DateTime` specifically.

```php
use DateTime;

$dateTime = $phodam->create(DateTime::class);
echo $dateTime->format('Y-m-d H:i:s'); // Current date and time
```

## DateTimeImmutable

Generates a `DateTimeImmutable` instance representing the current date and time. Use this when you need an immutable date object that prevents accidental modifications, which is recommended for value objects and functional programming patterns.

```php
use DateTimeImmutable;

$dateTimeImmutable = $phodam->create(DateTimeImmutable::class);
$newDate = $dateTimeImmutable->modify('+1 day'); // Returns a new instance
```

## DateInterval

Generates a `DateInterval` instance representing a 1-day interval (`P1D`). Use this when you need to perform date arithmetic operations such as adding or subtracting time from dates in your tests.

```php
use DateInterval;
use DateTime;

$interval = $phodam->create(DateInterval::class);
$date = new DateTime('2024-01-01');
$date->add($interval);
echo $date->format('Y-m-d'); // 2024-01-02
```

## DatePeriod

Generates a `DatePeriod` instance representing a period from the current date to 7 days in the future with daily intervals. Use this when you need to iterate over a range of dates or test date range validation logic.

```php
use DatePeriod;

$period = $phodam->create(DatePeriod::class);
foreach ($period as $date) {
    echo $date->format('Y-m-d') . "\n";
}
```

## DateTimeZone

Generates a `DateTimeZone` instance representing UTC timezone. Use this when you need to ensure consistent timezone handling in tests or when testing timezone conversion logic.

```php
use DateTimeZone;
use DateTimeImmutable;

$timezone = $phodam->create(DateTimeZone::class);
$date = new DateTimeImmutable('now', $timezone);
echo $date->getTimezone()->getName(); // UTC
```

## Summary

Built-in types provide default implementations for PHP's date and time classes, eliminating the need to manually construct these objects in tests. All types are automatically available when using `PhodamSchema::withDefaults()` and require no configuration. For custom behavior, create named providers.

| Type | Default Value | Use Case |
|------|---------------|----------|
| `DateTime` | Current date/time | Mutable date operations |
| `DateTimeImmutable` | Current date/time | Immutable date objects |
| `DateInterval` | 1 day (`P1D`) | Date arithmetic |
| `DatePeriod` | 7-day period from now | Date range iteration |
| `DateTimeZone` | UTC | Timezone handling |
