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

Generates a random integer value within a specified range.

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `min` | `int` | `-10000` | Minimum value (inclusive) |
| `max` | `int` | `10000` | Maximum value (inclusive) |

### Example

```php
$intValue = $phodam->create('int');
// Returns: A random integer between default `min` and `max`

$age = $phodam->create('int', null, [], ['min' => 18, 'max' => 100]);
// Returns: A random integer between 18 and 100
```

## Float (`float`)

Generates a random floating-point number with configurable range and precision.

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `min` | `float` | `-10000.0` | Minimum value (inclusive) |
| `max` | `float` | `10000.0` | Maximum value (inclusive) |
| `precision` | `int` | `2` | Number of decimal places |

### Example

```php
$floatValue = $phodam->create('float');
// Returns: A random float between default `min` and `max` with default `precision`

$gpa = $phodam->create('float', null, [], [
    'min' => 0.0,
    'max' => 4.0,
    'precision' => 2
]);
// Returns: A random float like 3.75, 2.89, etc.
```

## String (`string`)

Generates a random string with configurable character sets and length.

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

### Example

```php
$stringValue = $phodam->create('string');
// Returns a random alphanumeric string between default `minLength` and `maxLength`

$username = $phodam->create('string', config: [
    'type' => 'upper',
    'minLength' => 10,
    'maxLength' => 20
]);
// Returns: A random uppercase string between 10 and 20 characters
```

## Boolean (`bool`)

Generates a random boolean value.

### Configuration Options

The boolean provider does not accept any configuration options. It always returns a randomly generated `true` or `false` value.

### Example

```php
$isActive = $phodam->create('bool');
// Returns: Either true or false randomly
```

## Summary

Phodam provides built-in providers for all PHP primitive types (`int`, `float`, `string`, `bool`). Each provider accepts configuration options through the `$config` parameter to customize value generation. Integer and float providers support range constraints, float providers support precision control, and string providers offer flexible character set and length configuration. Boolean values are generated randomly without configuration options.
