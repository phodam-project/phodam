# Generating Primitive Types with Phodam

This document describes how to generate primitive types using `PhodamInterface` and the configuration options available for each type provider.

## Overview

All primitive types are generated using the `PhodamInterface::create()` method:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Generate a primitive type
$value = $phodam->create('int');  // or 'float', 'string', 'bool'
```

The `create()` method signature is:

```php
public function create(
    string $type,
    ?string $name = null,
    ?array $overrides = null,
    ?array $config = null
);
```

For primitive types, the `$config` parameter is used to customize how the values are generated.

## Integer (`int`)

Generates a random integer value.

### Basic Usage

```php
$int = $phodam->create('int');
// Returns: A random integer between -10000 and 10000 (default range)
```

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `min` | `int` | `-10000` | Minimum value (inclusive) |
| `max` | `int` | `10000` | Maximum value (inclusive) |

### Examples

**Generate a positive integer:**
```php
$positiveInt = $phodam->create(
    'int',
    null,
    [],
    ['min' => 0, 'max' => PHP_INT_MAX]
);
// Returns: A random integer between 0 and PHP_INT_MAX
```

**Generate an integer within a specific range:**
```php
$age = $phodam->create(
    'int',
    null,
    [],
    ['min' => 18, 'max' => 100]
);
// Returns: A random integer between 18 and 100
```

**Generate a small range integer:**
```php
$diceRoll = $phodam->create(
    'int',
    null,
    [],
    ['min' => 1, 'max' => 6]
);
// Returns: A random integer between 1 and 6
```

## Float (`float`)

Generates a random floating-point number.

### Basic Usage

```php
$float = $phodam->create('float');
// Returns: A random float between -10000.0 and 10000.0 with 2 decimal places (default)
```

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `min` | `float` | `-10000.0` | Minimum value (inclusive) |
| `max` | `float` | `10000.0` | Maximum value (inclusive) |
| `precision` | `int` | `2` | Number of decimal places |

### Examples

**Generate a test score (0.0 to 100.0 with 1 decimal place):**
```php
$testScore = $phodam->create(
    'float',
    null,
    [],
    [
        'min' => 0.0,
        'max' => 100.0,
        'precision' => 1
    ]
);
// Returns: A random float like 87.5, 92.3, etc.
```

**Generate a GPA (0.0 to 4.0 with 2 decimal places):**
```php
$gpa = $phodam->create(
    'float',
    null,
    [],
    [
        'min' => 0.0,
        'max' => 4.0,
        'precision' => 2
    ]
);
// Returns: A random float like 3.75, 2.89, etc.
```

**Generate a percentage with high precision:**
```php
$percentage = $phodam->create(
    'float',
    null,
    [],
    [
        'min' => 0.0,
        'max' => 1.0,
        'precision' => 4
    ]
);
// Returns: A random float like 0.8234, 0.1592, etc.
```

## String (`string`)

Generates a random string with configurable character sets and length.

### Basic Usage

```php
$string = $phodam->create('string');
// Returns: A random alphanumeric string between 16 and 32 characters (default)
```

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `type` | `string` | `'alphanumeric'` | Character set type (see below) |
| `minLength` | `int` | `16` | Minimum string length |
| `maxLength` | `int` | `32` | Maximum string length |
| `length` | `int` | `null` | Exact string length (overrides minLength/maxLength) |

**String Types:**
- `'lower'` - Lowercase letters only (`a-z`)
- `'upper'` - Uppercase letters only (`A-Z`)
- `'alphabetic'` - Both uppercase and lowercase letters (`a-z`, `A-Z`)
- `'numeric'` - Numbers only (`0-9`)
- `'alphanumeric'` - Letters and numbers (`a-z`, `A-Z`, `0-9`) - **default**

### Examples

**Generate an uppercase string with specific length range:**
```php
$uppercaseString = $phodam->create(
    'string',
    null,
    [],
    [
        'minLength' => 10,
        'maxLength' => 20,
        'type' => 'upper'
    ]
);
// Returns: A random uppercase string like "KLMNOPQRST", "ABCDEFGHIJKLM", etc.
```

**Generate a string with exact length:**
```php
$exactLength = $phodam->create(
    'string',
    null,
    [],
    [
        'length' => 18
    ]
);
// Returns: A random alphanumeric string exactly 18 characters long
```

**Generate a lowercase alphabetic string:**
```php
$lowercase = $phodam->create(
    'string',
    null,
    [],
    [
        'type' => 'lower',
        'minLength' => 5,
        'maxLength' => 10
    ]
);
// Returns: A random lowercase string like "abcdef", "xyzabcde", etc.
```

**Generate a numeric string:**
```php
$numericString = $phodam->create(
    'string',
    null,
    [],
    [
        'type' => 'numeric',
        'length' => 10
    ]
);
// Returns: A random numeric string like "1234567890", "9876543210", etc.
```

**Generate an alphabetic string (letters only, mixed case):**
```php
$alphabetic = $phodam->create(
    'string',
    null,
    [],
    [
        'type' => 'alphabetic',
        'minLength' => 8,
        'maxLength' => 15
    ]
);
// Returns: A random alphabetic string like "AbCdEfGh", "XyZaBcDeFgHi", etc.
```

**Note:** When `length` is specified, it takes precedence over `minLength` and `maxLength`. The string will have exactly the specified length.

## Boolean (`bool`)

Generates a random boolean value.

### Basic Usage

```php
$bool = $phodam->create('bool');
// Returns: A random boolean value (true or false)
```

### Configuration Options

The boolean provider does not accept any configuration options. It always returns a randomly generated `true` or `false` value.

### Examples

**Simple boolean generation:**
```php
$isActive = $phodam->create('bool');
// Returns: Either true or false randomly
```

## Complete Example

Here's a complete example showing all primitive types:

```php
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;

$schema = PhodamSchema::withDefaults();
$phodam = $schema->getPhodam();

// Generate an integer (positive, up to 100)
$age = $phodam->create('int', null, [], ['min' => 0, 'max' => 100]);

// Generate a float (GPA between 0.0 and 4.0)
$gpa = $phodam->create('float', null, [], [
    'min' => 0.0,
    'max' => 4.0,
    'precision' => 2
]);

// Generate a string (uppercase, 10-20 characters)
$username = $phodam->create('string', null, [], [
    'type' => 'upper',
    'minLength' => 10,
    'maxLength' => 20
]);

// Generate a boolean
$isActive = $phodam->create('bool');
```

## Method Signature Reference

When calling `create()` for primitive types:

```php
$value = $phodam->create(
    string $type,           // 'int', 'float', 'string', or 'bool'
    ?string $name = null,   // Always null for primitive types
    ?array $overrides = null, // Always [] for primitive types
    ?array $config = null    // Configuration array (see above)
);
```

