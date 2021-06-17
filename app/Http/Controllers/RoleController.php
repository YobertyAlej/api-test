<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionNotFoundException;
use App\Exceptions\PermissionNotFoundOnRoleException;
use App\Exceptions\RoleNotCreatedException;
use App\Exceptions\RoleNotDeletedException;
use App\Exceptions\RoleNotFoundException;
use App\Exceptions\RoleNotUpdatedException;
use App\Http\Requests\DeleteRoleRequest;
use App\Http\Requests\GrantPermissionToRoleRequest;
use App\Http\Requests\RevokePermissionToRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    private $role_repo;

    public function __construct(RoleRepository $role_repo)
    {
        $this->role_repo = $role_repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('viewAny', Role::class);
        $data = $this->role_repo->paginate(10);

        return RoleResource::collection($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  $role
     * @return \Illuminate\Http\Response
     */
    public function show($role)
    {
        Gate::authorize('view', Role::class);

        try {
            $role = $this->role_repo->findOrFail($role);
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$role} does not exist"
            ], 404);
        }

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     *
     * @param  $user
     * @return \Illuminate\Http\Response
     */
    public function showByName($name)
    {
        Gate::authorize('view', Role::class);

        try {
            $role = $this->role_repo->findByNameOrFail($name);
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$name} does not exist"
            ], 404);
        }

        return new RoleResource($role);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param App\Http\Requests\StoreRoleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            $role = $this->role_repo->create($request->all());
        } catch (RoleNotCreatedException $e) {
            return response()->json([
                'error' => "The role could not be created"
            ], 500);
        }

        return response()->json(
            ['message' => "Successfully created the role {$role->name} with the label {$role->label}"],
            201
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateRoleRequest $request
     * @param  $role_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $role_id)
    {
        try {
            $role = $this->role_repo->update($role_id, $request->all());
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$role} could not be found"
            ], 404);
        } catch (RoleNotUpdatedException $e) {
            return response()->json([
                'error' => "The role {$role} could not be updated"
            ], 500);
        }

        return response()->json(
            ['message' => "Successfully updated the role {$role->label}"]
        );
    }

    /**
     * Remove the specified resource in storage.
     *
     * @param  App\Http\Requests\DeleteRoleRequest $request
     * @param  $role_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRoleRequest $request, $role_id)
    {
        try {
            $this->role_repo->delete($role_id);
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$role_id} could not be found"
            ], 404);
        } catch (RoleNotDeletedException $e) {
            return response()->json([
                'error' => "The role {$role_id} could not be delete"
            ], 500);
        }

        return response()->json(
            ['message' => "Successfully deleted the role {$role_id}"],
            204
        );
    }

    /**
     * Grant a permission to a role
     *
     * @param  App\Http\Requests\GrantPermissionToRoleRequest $request
     * @param  $role
     * @return \Illuminate\Http\Response
     */
    public function grantPermission(GrantPermissionToRoleRequest $request, $role)
    {
        $permission = $request->input('permission');

        try {
            $role = $this->role_repo->findByNameOrFail($role);
            $role->allowTo($permission);
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$role} does not exist"
            ], 404);
        } catch (PermissionNotFoundException $e) {
            return response()->json([
                'error' => "The permission {$permission} does not exist"
            ], 404);
        }

        return response()->json(
            ['message' => "Successfully granted the permission {$permission} to the role {$role->name}"]
        );
    }

    /**
     * Revoke a permission to a role
     *
     * @param  App\Http\Requests\RevokePermissionToRoleRequest $request
     * @param  $role
     * @return \Illuminate\Http\Response
     */
    public function revokePermission(RevokePermissionToRoleRequest $request, $role)
    {
        $permission = $request->input('permission');

        try {
            $role = $this->role_repo->findByNameOrFail($role);
            $role->revokePermission($permission);
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$role} does not exist"
            ], 404);
        } catch (PermissionNotFoundException $e) {
            return response()->json([
                'error' => "The permission {$permission} does not exist"
            ], 404);
        } catch (PermissionNotFoundOnRoleException $e) {
            return response()->json([
                'error' => "The permission {$permission} is not assigned to the role {$role->name}"
            ], 404);
        }

        return response()->json(
            ['message' => "Successfully revoked the permission {$permission} to the role {$role->name}"]
        );
    }
}
