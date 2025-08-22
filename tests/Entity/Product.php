<?php

namespace NickoCh\Utils\Tests\Entity;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use NickoCh\Utils\Entity\Property;

class Product extends Property
{
    #[SerializedName("productid")]
    #[Type("int")]
    public $id;

    #[SerializedName("product_name")]
    #[Type("string")]
    public $name;

    #[SerializedName("product_price")]
    #[Type("int")]
    public $price;
}