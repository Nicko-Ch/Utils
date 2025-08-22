<?php

namespace NickoCh\Utils\Tests;

use NickoCh\Utils\Tests\Entity\Order;
use NickoCh\Utils\Tests\Entity\Product;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{

    public const ORDER_JSON_0 = '{"orderid":1,"name":"订单","products":null}';
    public const ORDER_JSON_1 = '{"orderid":1,"name":"订单","products":[{"productid":101,"product_name":"apple","product_price":null}]}';

    public function testSerializer(): void
    {
        $order       = new Order();
        $order->id   = 1;
        $order->name = "订单";

        $this->assertEquals(self::ORDER_JSON_0, $order->serialize());
    }

    public function testDeserializer(): void
    {
        $order = Order::make()->deserialize(self::ORDER_JSON_1);

        $this->assertEquals(1, $order->id);
        $this->assertEquals("订单", $order->name);
        $this->assertEquals(101, $order->products[0]->id);
        $this->assertEquals("apple", $order->products[0]->name);
    }

    public function testFillAndSerialize(): void
    {
        $orderSerializer = Order::make([
            "id"       => 1,
            "name"     => "订单",
            "products" => [
                Product::make([
                    "id"    => 101,
                    "name"  => "apple",
                    "price" => null,
                ]),
            ],
        ])->serialize();

        $this->assertEquals(self::ORDER_JSON_1, $orderSerializer);
    }
}