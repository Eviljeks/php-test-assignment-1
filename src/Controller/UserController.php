<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Services\UserPasswordEncoder;
use App\Services\UserFiller;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends AbstractController
{
    private UserRepository $userRepo;
    private ValidatorInterface $validator;
    private UserFiller $userFiller;

    public function __construct(
        UserRepository $userRepo,
        ValidatorInterface $validator,
        UserFiller $userFiller
    ) {
        $this->userRepo = $userRepo;
        $this->validator = $validator;
        $this->userFiller = $userFiller;
    } 

    /**
     * @Route("/user", name="user_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $user = $this->userFiller->fill($request->toArray());

        $violations = $this->validator->validate($user);

        if (count($violations) > 0) {
            return $this->failResponse($this->getValidationErrorMessages($violations));
        }

        if (null !== $this->userRepo->findByEmail($user->getEmail()) || null !== $this->userRepo->findByUsername($user->getUsername())) {
            return $this->failResponse(['User with such email/username already exists.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->userRepo->save($user);

        return $this->successResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(), 
            'username' => $user->getUsername(),
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", name="user_update", methods={"PATCH"})
     */
    public function update(Request $request, string $id): Response
    {
        /** @var  User|null $user */
        $user = $this->userRepo->findById($id);

        if (null === $user) {
            return $this->failResponse(['User not found'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->userFiller->fill($request->toArray(), $user);
        
        $violations = $this->validator->validate($user);

        if (count($violations) > 0) {
            return $this->json(['errors' => $this->getValidationErrorMessages($violations)]);
        }

        $this->userRepo->save($user);

        return $this->successResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(), 
            'username' => $user->getUsername(),
        ]);
    }

    /**
     * @Route("/user", name="user_one", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');

        if (null !== $search) {
            $users = $this->userRepo->findByEmailOrUsername($search);
        } else {
            $users = $this->userRepo->findAll();
        }

        return $this->successResponse($users);
    }

    private function successResponse(array $data, int $status = Response::HTTP_OK)
    {
        return $this->json(['data' => $data, 'errors' => null], $status);
    }

    private function failResponse(array $errors, int $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->json(['data' => null, 'errors' => $errors], $status);
    }

    /**
     * @var ConstraintViolationListInterface 
     */
    private function getValidationErrorMessages(ConstraintViolationListInterface $violations)
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'path' => $violation->getPropertyPath(), 
                'message' => $violation->getMessage(),
            ];
        }
        return $errors;
    }
}
