<?php

namespace App\Repositories;

use App\Exceptions\RoleNotCreatedException;
use App\Exceptions\RoleNotDeletedException;
use App\Exceptions\RoleNotFoundException;
use App\Exceptions\RoleNotUpdatedException;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class RoleRepository
{
    public function paginate($results)
    {
        return Role::paginate($results);
    }

    public function findOrFail($id)
    {
        try {
            $role = Role::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException;
        }

        return $role;
    }

    public function findByNameOrFail($name)
    {
        try {
            $role = Role::where('name', $name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException;
        }

        return $role;
    }

    public function create($payload)
    {
        try {
            $role = Role::create($payload);
        } catch (QueryException $e) {
            throw new RoleNotCreatedException;
        }

        return $role;
    }

    public function update($role_id, $payload)
    {
        try {
            $role = Role::findOrFail($role_id);
            $role->update($payload);
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException;
        } catch (QueryException $e) {
            throw new RoleNotUpdatedException;
        }

        return $role;
    }

    public function delete($role_id)
    {
        try {
            $role = Role::findOrFail($role_id);
            $role->delete();
        } catch (ModelNotFoundException $e) {
            throw new RoleNotFoundException;
        } catch (QueryException $e) {
            throw new RoleNotDeletedException;
        }

        return $role;
    }

    public function getSuperAdmin()
    {
        return Role::where('name', Role::$SUPERADMIN)->first();
    }
}
