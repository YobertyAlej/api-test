<?php

namespace App\Repositories;

use App\Exceptions\UserNotCreatedException;
use App\Exceptions\UserNotDeletedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotUpdatedException;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class UserRepository
{
    public function paginate($results)
    {
        return User::paginate($results);
    }

    public function findOrFail($id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException;
        }

        return $user;
    }

    public function findByEmailOrFail($email)
    {
        try {
            $user = User::where('email', $email)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException;
        }

        return $user;
    }
    

    public function create($payload)
    {
        try {
            $user = User::create($payload);
        } catch (QueryException $e) {
            throw new UserNotCreatedException;
        }

        return $user;
    }

    public function update($user_id, $payload)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->update($payload);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException;
        } catch (QueryException $e) {
            throw new UserNotUpdatedException;
        }

        return $user;
    }

    public function delete($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->delete();
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException;
        } catch (QueryException $e) {
            throw new UserNotDeletedException;
        }

        return $user;
    }
}