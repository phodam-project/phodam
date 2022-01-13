<?php

namespace Phodam;


use ReflectionClass;
use ReflectionProperty;

class Phodam
{

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $overrides
     * @return T
     */
    public function create(string $class, array $overrides = []): object {
        $result = $this->populateObject($class, $overrides);
        return $result;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $fields
     * @return T
     */
    private function populateObject(string $class, array $overrides): object
    {
        $refClass = new ReflectionClass($class);
        $obj = $refClass->newInstanceWithoutConstructor();
        foreach ($refClass->getProperties() as $field) {
            $this->populateValue($obj, $field, $overrides);
        }
        return $obj;
    }

    /**
     * @param object $obj
     * @param ReflectionProperty $property
     * @param array<string, mixed> $overrides
     * @return void
     */
    private function populateValue(object $obj, ReflectionProperty &$property, array $overrides): void
    {
        $val = null;
        if (array_key_exists($property->getName(), $overrides)) {
            $val = $overrides[$property->getName()];
        } else {
            switch ($property->getType()->getName()) {
                case "int":
                    $val = $this->randomInt();
                    break;
                case "string":
                    $val = $this->randomString();
                    break;
                case "float":
                    $val = $this->randomFloat();
                    break;
                case "bool":
                    $val = $this->randomBool();
                    break;
                case "array":
                    $val = $this->randomArray();
                    break;
                case "object":
                    $val = $this->randomObject();
                    break;
                default:
                    break;
            }
        }
        $property->setAccessible(true);
        $property->setValue($obj, $val);
    }

    private function randomInt(): int
    {
        return rand(0, 1000);
    }

    private function randomFloat(): float
    {
        return (float) $this->randomInt() / 100.0;
    }

    private function randomString(): string
    {
        $md5 = md5($this->randomInt() . " " . $this->randomInt());
        $len = strlen($md5);
        return substr($md5, 0, rand(5, $len - 1 - 5));
    }

    private function randomBool(): bool
    {
        return (bool) rand(0, 1);
    }

    /**
     * @return list<string>
     */
    private function randomArray(): array
    {
        $result = [];
        for ($i = 0; $i < rand(3, 5); ++$i) {
            $result[] = $this->randomString();
        }
        return $result;
    }

    private function randomObject(): object
    {
        $obj = new \stdClass();
        for ($i = 0; $i < rand(3, 5); ++$i) {
            $obj->{$this->randomString()} = $this->randomString();
        }
        return $obj;
    }
}