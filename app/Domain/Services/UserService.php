<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Contracts\Ports\In\IUserService;
use App\Domain\Contracts\Ports\Out\IUserRepository;
use App\Domain\Contracts\Ports\Out\DeletedUserOutput;
use App\Domain\Contracts\Ports\Out\UserListOutput;
use App\Domain\Contracts\Ports\Out\UserOutput;
use App\Domain\ValueObjects\UserData;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\UserNotFoundException;

class UserService implements IUserService
{
    // @todo utilizar serviço de auth para eliminar sessões
    public function __construct(private IUserRepository $userRepository)
    {
    }

    public function getUsers(): UserListOutput
    {
        return new UserListOutput($this->userRepository->all());
    }

    public function createUser(UserData $userData): UserOutput
    {
        $user = $this->userRepository->save($userData);
        
        if (empty($user)) {
            throw new UserAlreadyExistsException();
        }

        return new UserOutput($user);
    }

    public function updateUser(UserData $userData): UserOutput
    {
        $user = $this->userRepository->update($userData);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        return new UserOutput($user);
    }

    public function getUserById(int $id): UserOutput
    {
        $user = $this->userRepository->getById($id);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        return new UserOutput($user);
    }

    public function deleteUser(int $id): DeletedUserOutput
    {
        $wasDeleted = $this->userRepository->delete($id);

        if (! $wasDeleted) {
            throw new UserNotFoundException();
        }

        return new DeletedUserOutput();
    }
}
