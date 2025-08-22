<?php

declare(strict_types=1);

namespace NickoCh\Utils\Entity;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Visitor\Factory\JsonSerializationVisitorFactory;
use NickoCh\Utils\Exception\EntityException;

class Property
{
    public static function make(array $properties = []): static
    {
        $obj = new static;
        if ($properties) {
            return $obj->fillProperty($properties);
        }
        return $obj;
    }

    public function takeAllProperty(): array
    {
        foreach ($this as $key => $value) {
            $result[$key] = $value;
        }
        return $result ?? [];
    }

    public function fillProperty(array $properties): self
    {
        foreach ($properties as $property => $value) {
            if (!is_string($property) || !property_exists($this, $property)) {
                continue;
            }
            $this->$property = $value ?? null;
        }
        return $this;
    }

    /**
     * @throws EntityException
     */
    public function __call($methodName, array $args = [])
    {
        $offset    = 0;
        $length    = 3;
        $action    = substr($methodName, $offset, $length);
        $attribute = lcfirst(substr($methodName, $length + $offset));
        switch ($action) {
            case 'get':
                return $this->$attribute ?? null;
            case 'set':
                $this->$attribute = $args[0];
                return $this;
        }
        throw new EntityException("unknown method name: {$methodName}");
    }

    public function getSerializer(): Serializer
    {
        $vistor = new JsonSerializationVisitorFactory();
        $vistor->setOptions(JSON_UNESCAPED_UNICODE);

        return SerializerBuilder::create()
            ->setSerializationVisitor('json', $vistor)
            ->setSerializationContextFactory(function () {
                return SerializationContext::create()
                    ->setSerializeNull(true);
            })
            ->build();
    }

    public function serialize(string $format = 'json', ?SerializationContext $context = null, ?string $type = null): string
    {
        return $this->getSerializer()->serialize($this, $format, $context, $type);
    }

    public function deserialize(string $jsonData, string $format = 'json', ?DeserializationContext $context = null): static
    {
        $object = $this->getSerializer()->deserialize($jsonData, get_class($this), $format, $context);

        foreach ($object as $property => $value) {
            $this->$property = $value;
        }
        return $this;
    }
}
