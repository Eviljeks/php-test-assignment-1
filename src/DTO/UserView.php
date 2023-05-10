<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\User;

final class UserView
{
    private int $id;
    private string $email;
    private string $username;

    public function __construct(int $id, string $email, string $username)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
        ];
    }

    public static function fromUser(User $user): UserView
    {
        return new UserView(
            $user->getId(),
            $user->getEmail(),
            $user->getUsername()
        );
    }
}