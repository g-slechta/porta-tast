<?php

namespace App\Transformer;


use InvalidArgumentException;

class ArrayTransform
{
    public function concat(array $data, string $separator, string|int ... $keys): string
    {
        $result = '';
        foreach ($keys as $key) {
            if($result) $result .= $separator;
            $result .= $this->string($data, $key);
        }
        return $result;
    }

    public function string(array $data, string|int $key): string
    {
        if(isset($data[$key]) && is_string($data[$key])) {
            return $data[$key];
        }
        throw new InvalidArgumentException("$key is not a string");
    }

    public function map(array $data, string|int $key, array $valueMap): mixed
    {
        if(!isset($data[$key]) ) {
            throw new InvalidArgumentException("$key is not a set");
        }
        if(!isset($valueMap[$data[$key]])) {
            throw new InvalidArgumentException("{$key} '{$data[$key]}' is not mapped");
        }
        return $valueMap[$data[$key]];
    }

    public function hash(array $data, string|int $key, $algo = PASSWORD_DEFAULT):string
    {
        return password_hash($this->string($data, $key), $algo);

    }
}