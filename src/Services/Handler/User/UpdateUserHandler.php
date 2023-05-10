<?php

declare(strict_types=1);

namespace App\Services\Handler\User;

use App\DTO\CreateUserDTO;
use App\DTO\UpdateUserDTO;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Services\UserPasswordEncoder;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserHandler
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

    public function handle(UpdateUserDTO $updateUserDTO): User
    {
        /** @var  User|null $user */
        $user = $this->userRepo->findById($updateUserDTO->getId());

        if (null === $user) {
            throw new UserNotFoundException('User not found');
        }

        $user->setEmail($updateUserDTO->getEmail());
        $user->setUsername($updateUserDTO->getUsername());
        $user->setPassword($this->userPasswordEncoder->encode($updateUserDTO->getPassword()));

        $this->userRepo->save($user);

        return $user;
    }
}