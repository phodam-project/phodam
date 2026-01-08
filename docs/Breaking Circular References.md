# Breaking Circular References

Phodam can generate cyclic graphs by passing pre-built instances into nested create calls. The `Ex07_BreakingCircularReferences` example shows how to keep a parent reference intact without infinite recursion.

## Example

Use a custom provider to create the parent first, then inject it into each child so the back-reference is reused rather than re-created.

```php
#[PhodamProvider(Order::class)]
class OrderTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): Order
    {
        $order = new Order();

        $numItems = $context->getConfig()['numItems'] ?? 2;

        $defaults = [
            'id' => $context->getPhodam()->create('int'),
            'items' => array_map(
                fn ($i) => $context->getPhodam()->create(OrderItem::class, overrides: ['order' => $order]),
                range(0, $numItems)
            ),
        ];

        $values = array_merge($defaults, $context->getOverrides());

        return $order
            ->setId($values['id'])
            ->setItems($values['items']);
    }
}
```

```php
$schema = PhodamSchema::withDefaults();
$schema->registerProvider(OrderTypeProvider::class);

$order = $schema->getPhodam()->create(Order::class);
// $order->getItems() each hold the same $order instance
```

## Summary

Register a custom provider, construct the parent first, inject it into child overrides, and let Phodam finish populating the graph without recursion issues.
