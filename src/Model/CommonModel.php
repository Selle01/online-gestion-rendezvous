<?php

namespace App\Model;


class CommonModel
{
    public static function hydrate($object, array $data, array $fields): void
    {
        foreach ($fields as  $field) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            $object->$method($data[$field]);
        }
    }

    public function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            return $value->format('y-m-d H:i:s');
        }
        return (string) $value;
    }
}
