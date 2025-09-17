<?php

namespace App\Transformer;

use App\Transformer\ArrayTransform;

class UserArrayTransform extends ArrayTransform
{
    public function transform(array $userData, $hashPassword = false): array
    {
        $result = [];
        $result['id'] = $this->string($userData, 'ID');
        $result['full_name'] = $this->concat($userData, ' ', 'First name', 'Last name');
        $result['email'] = $this->string($userData, 'email');
        $result['password'] = $hashPassword?$this->hash($userData, 'password'):$this->string($userData, 'password');
        $result['status'] = $this->map($userData, 'status', ['1' => 'ACTIVE', '2' => 'INACTIVE']);
        return $result;

    }
}