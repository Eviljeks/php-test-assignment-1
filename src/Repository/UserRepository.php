<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findById(int $id): ?User
    {
        return $this->findOneBy(compact('id'));
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(compact('email'));
    }

    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy(compact('username'));
    }

    /**
     * @return User[]
     */
    public function findByEmailOrUsername(string $value): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email LIKE :val OR u.username LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }
}
