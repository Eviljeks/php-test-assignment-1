<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;

final class UserFiller {

    private UserPasswordEncoder $userPasswordEncoder;

    public function __construct(UserPasswordEncoder $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function fill(array $data, ?User $user = null): User
    {
        if (null === $user) {
            return new User($data['email'], $data['username'], $this->userPasswordEncoder->encode($data['password']));
        } 
        
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setPassword($this->userPasswordEncoder->encode($data['password']));

        return $user;
    }
}
