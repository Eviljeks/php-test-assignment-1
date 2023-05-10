<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreateUserDTO;
use App\DTO\ListUsersDTO;
use App\DTO\UpdateUserDTO;
use App\DTO\UserView;
use App\Exception\UserAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Services\Handler\User\CreateUserHandler;
use App\Services\Handler\User\ListUsersHandler;
use App\Services\Handler\User\UpdateUserHandler;
use App\Services\JsonRequestDecoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends AbstractController
{
    private ValidatorInterface $validator;

    private CreateUserHandler $createUserHandler;
    private UpdateUserHandler $updateUserHandler;
    private ListUsersHandler $listUsersHandler;
    private JsonRequestDecoder $jsonRequestDecoder;

    public function __construct(
        ValidatorInterface $validator,
        CreateUserHandler $createUserHandler,
        UpdateUserHandler $updateUserHandler,
        ListUsersHandler  $listUsersHandler,
        JsonRequestDecoder $jsonRequestDecoder
    ) {
        $this->validator = $validator;
        $this->createUserHandler = $createUserHandler;
        $this->updateUserHandler = $updateUserHandler;
        $this->listUsersHandler = $listUsersHandler;
        $this->jsonRequestDecoder = $jsonRequestDecoder;
    }

    /**
     * @Route("/user", name="user_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $params = $this->jsonRequestDecoder->decodeRequest($request);
        $email = $params->get('email');
        $username = $params->get('username');
        $password = $params->get('password');

        if ($email === null || $username === null || $password === null) {
            throw new BadRequestHttpException('Required fields are "email", "username", "password"');
        }

        $createUserDTO = new CreateUserDTO(
            $email,
            $username,
            $password,
        );

        $violations = $this->validator->validate($createUserDTO);

        if (count($violations) > 0) {
            return $this->failResponse($this->getValidationErrorMessages($violations));
        }

       try {
            $user = $this->createUserHandler->handle($createUserDTO);
       } catch (UserAlreadyExistsException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
       } catch (\Throwable $e) {
            $this->throwInternalServerError();
       }

        return $this->successResponse(UserView::fromUser($user)->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{id}", name="user_update", methods={"PATCH"})
     */
    public function update(Request $request, int $id): Response
    {
        $params = $this->jsonRequestDecoder->decodeRequest($request);

        $email = $params->get('email');
        $username = $params->get('username');
        $password = $params->get('password');

        if ($email === null || $username === null || $password === null) {
            throw new BadRequestHttpException('Required fields are "id", "email", "username", "password"');
        }

        $updateUserDTO = new UpdateUserDTO(
            $id,
            $email,
            $username,
            $password,
        );

        $violations = $this->validator->validate($updateUserDTO);

        if (count($violations) > 0) {
            return $this->json(['errors' => $this->getValidationErrorMessages($violations)]);
        }

        try {
            $user = $this->updateUserHandler->handle($updateUserDTO);
        } catch (UserNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        } catch (\Throwable $e) {
            $this->throwInternalServerError();
        }

        return $this->successResponse(UserView::fromUser($user)->toArray());
    }

    /**
     * @Route("/user", name="user_one", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');

        $listUsersDTO = new ListUsersDTO($search);

        $users = $this->listUsersHandler->handle($listUsersDTO);

        return $this->successResponse(array_map(fn (User $user) => UserView::fromUser($user)->toArray(), $users));
    }

    private function successResponse(array $data, int $status = Response::HTTP_OK): Response
    {
        return $this->json(['data' => $data, 'errors' => null], $status);
    }

    private function failResponse(array $errors, int $status = Response::HTTP_INTERNAL_SERVER_ERROR): Response
    {
        return $this->json(['data' => null, 'errors' => $errors], $status);
    }

    /**
     * @var ConstraintViolationListInterface 
     */
    private function getValidationErrorMessages(ConstraintViolationListInterface $violations): array
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

    private function throwInternalServerError(): void
    {
        throw new HttpException(500, 'Unknown error occurred.');
    }
}
