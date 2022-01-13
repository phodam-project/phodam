<?php

namespace Tests\Phodam;

use Phodam\Phodam;
use PHPUnit\Framework\TestCase;

class PhodamTest extends TestCase
{
    private Phodam $phodam;

    public function setUp(): void {
        $this->phodam = new Phodam();
    }

    public function test(): void
    {
        /** @var TestClass $item */
        $item = $this->phodam->create(TestClass::class);
        $this->assertIsString($item->getMyString());
        $this->assertIsInt($item->getMyInt());
        $this->assertIsFloat($item->getMyFloat());
        $this->assertIsBool($item->isMyBool());
        $this->assertIsArray($item->getMyArray());
        $this->assertIsObject($item->getMyObject());
    }

    public function testOverrides(): void
    {
        $item = $this->phodam->create(TestClass::class, [
            'myString' => 'my cool string',
            'myInt' => 420
        ]);
        var_export($item);

        $this->assertEquals('my cool string', $item->getMyString());
        $this->assertEquals(1337, $item->getMyInt());
    }

    public function testDifferent(): void
    {
        $base = $this->phodam->create(TestClass::class);
        for ($i = 0; $i < 10; ++$i) {
            $current = $this->phodam->create(TestClass::class);
            $this->assertNotEqualsCanonicalizing($current, $base);
//            echo $i . ":\n";
//            echo "```\n";
//            echo var_export($current, true);
//            echo "\n```\n";
        }
    }
}

class TestClass
{
    private string $myString;
    private int $myInt;
    private float $myFloat;
    private bool $myBool;
    private array $myArray;
    private object $myObject;

    /**
     * @return string
     */
    public function getMyString(): string
    {
        return $this->myString;
    }

    /**
     * @return int
     */
    public function getMyInt(): int
    {
        return $this->myInt;
    }

    /**
     * @return float
     */
    public function getMyFloat(): float
    {
        return $this->myFloat;
    }

    /**
     * @return bool
     */
    public function isMyBool(): bool
    {
        return $this->myBool;
    }

    /**
     * @return array
     */
    public function getMyArray(): array
    {
        return $this->myArray;
    }

    /**
     * @return object
     */
    public function getMyObject(): object
    {
        return $this->myObject;
    }
}