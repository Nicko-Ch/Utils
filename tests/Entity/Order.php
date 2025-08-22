<?php

namespace NickoCh\Utils\Tests\Entity;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use NickoCh\Utils\Entity\Property;

class Order extends Property
{
    #[SerializedName("orderid")]
    #[Type("int")]
    #[Groups(["detail"])]
    public $id;

    #[Type("string")]
    public $name;

    #[Type("array<NickoCh\Utils\Tests\Entity\Product>")]
    public $products;
}