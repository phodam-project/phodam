# Phodam

Phodam is inspired by [PODAM](https://mtedone.github.io/podam/).

Phodam (pronounced Faux-dam) is a PHP library used to generate objects for unit tests. The main feature of PODAM is that you can give it a class and it generates a populated Object with all fields populated.

Phodam, in its current state, will populate objects as long as it's given a `TypeProviderInterface` for a specific class.

## Usage

### Basic Usage
```php
$value = $this->phodam->create(SimpleType::class);
```

### Populating a Type with untyped fields
```php
// Since PHP classes don't require types on fields, you may need to provide some hints
// You only need to provide field definitions for fields that can't automatically be mapped
$definition = [
    'myInt' => new FieldDefinition('int'),
    'myString' => new FieldDefinition('string')
];

$this->phodam->registerTypeDefinition(SimpleTypeMissingSomeFieldTypes::class, $definition);

$value = $this->phodam->create(SimpleTypeMissingSomeFieldsTypes::class);
```

### Populating a Type with array (list) fields
```php
$definition = [
    'myArray' => (new FieldDefinition(SimpleType::class))
        ->setArray(true)
];

$this->phodam->registerTypeDefinition(SimpleTypeWithAnArray::class, $definition);

$value = $this->phodam->create(SimpleTypeWithAnArray::class);
```

## Local Build

```sh
./quickBuild.sh
```
