<?php

namespace App\Tests;

use App\Transformer\UserArrayTransform;
use PHPUnit\Framework\TestCase;

class UserTransformTest extends TestCase
{
    private UserArrayTransform $transformer;
    private array $sourceUser;
    private array $resultUser;

    protected function setUp(): void
    {
        $this->transformer = new UserArrayTransform();
        $this->sourceUser = [
            0 => [
                'ID' => '8a2148f2-ab3b-4c70-a974-d1a8b23e647a',
                'First name' => 'Sarah',
                'Last name' => 'Martin',
                'email' => 'sarah.martin@example.com',
                'password' => 'abc123',
                'status' => '1'
            ],
            1 => [
                'ID' => '9d91d9b7-c4c7-4de8-8982-b7de919d2691',
                'First name' => 'Colleen',
                'Last name' => 'Wiggins',
                'email' => 'colleen.wiggins@example.com',
                'password' => '123456789',
                'status' => '2'
            ]
        ];
        $this->resultUser = [
            0 => [
                'id' => '8a2148f2-ab3b-4c70-a974-d1a8b23e647a',
                'full_name' => 'Sarah Martin',
                'email' => 'sarah.martin@example.com',
                'password' => 'abc123',
                'status' => 'ACTIVE'
            ],
            1 => [
                'id' => '9d91d9b7-c4c7-4de8-8982-b7de919d2691',
                'full_name' => 'Colleen Wiggins',
                'email' => 'colleen.wiggins@example.com',
                'password' => '123456789',
                'status' => 'INACTIVE'
            ]
        ];
        parent::setUp();
    }

    public function testActiveUser()
    {
        $transformUser = $this->transformer->transform($this->sourceUser[0]);

        $this->assertSame($this->resultUser[0], $transformUser);
    }

    public function testInactiveUser()
    {
        $transformUser = $this->transformer->transform($this->sourceUser[1]);

        $this->assertSame($this->resultUser[1], $transformUser);
    }

    public function testUserPasswordHash()
    {
        $transformUser = $this->transformer->transform($this->sourceUser[0], true);
        $this->assertTrue(password_verify($this->sourceUser[0]['password'], $transformUser['password']));
    }

}