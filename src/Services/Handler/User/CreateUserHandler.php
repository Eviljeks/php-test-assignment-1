<?php

declare(strict_types=1);

namespace App\Services\Handler\User;

use App\DTO\CreateUserDTO;
use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Repository\UserRepository;
use App\Services\UserPasswordEncoder;

final class CreateUserHandler
{
    private UserRepository  $userRepo;
    private UserPasswordEncoder $userPasswordEncoder;

    public function __construct(
        UserRepository $userRepo,
        UserPasswordEncoder $userPasswordEncoder
    ) {
        $this->userRepo = $userRepo;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function handle(CreateUserDTO  $createUserDTO): User
    {
        $user = new User(
            $createUserDTO->getEmail(),
            $createUserDTO->getUsername(),
            $this->userPasswordEncoder->encode($createUserDTO->getPassword())
        );

        if (null !== $this->userRepo->findByEmail($user->getEmail()) || null !== $this->userRepo->findByUsername($user->getUsername())) {
            throw new UserAlreadyExistsException('User with such email/username already exists.');
        }

        $this->userRepo->save($user);

        return $user;
    }
}