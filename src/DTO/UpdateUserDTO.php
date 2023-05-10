<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserDTO
{
    private int $id;

    /**
     * @Assert\Email
     * @Assert\Length(min = 1, max = 180)
     */
    private string $email;

    /**
     * @Assert\Length(min = 1, max = 180)
     */
    private string $username;

    /**
     * @Assert\Length(min = 1, max = 255)
     */
    private string $password;

    public function __construct(int $id, string $email, string $username, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getId(): int
    {
        return $this->id;
    }
}