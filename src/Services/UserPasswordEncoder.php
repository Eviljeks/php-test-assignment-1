<?php

declare(strict_types=1);

namespace App\Services;

class UserPasswordEncoder
{
    private string $algorithm;

    public function __construct(string $algorithm = PASSWORD_ARGON2I)
    {
        $this->algorithm = $algorithm;
    }

    public function encode(string $password)
    {
        return password_hash($password, $this->algorithm);
    }
}