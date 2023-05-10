<?php

declare(strict_types=1);

namespace App\Services\Handler\User;

use App\DTO\ListUsersDTO;
use App\Entity\User;
use App\Repository\UserRepository;

final class ListUsersHandler
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param ListUsersDTO $listUsersDTO
     * @return User[]
     */
    public function handle(ListUsersDTO  $listUsersDTO): array
    {
        if (null !== $listUsersDTO->getSearch()) {
            $users = $this->userRepo->findByEmailOrUsername($listUsersDTO->getSearch());
        } else {
            $users = $this->userRepo->findAll();
        }

        return $users;
    }
}