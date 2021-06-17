<?php

namespace App\Http\Controllers;

use App\Exceptions\RoleNotFoundException;
use App\Exceptions\UserNotCreatedException;
use App\Exceptions\UserNotDeletedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotUpdatedException;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\GrantRoleToUserRequest;
use App\Http\Requests\RevokeRoleToUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    private $user_repo;

    public function __construct(UserRepository $user_repo)
    {
        $this->user_repo = $user_repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        return UserResource::collection($this->user_repo->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->user_repo->create($request->all());
        } catch (UserNotCreatedException $e) {
            return response()->json([
                'error' => "The user could not be created"
            ], 500);
        }

        return response()->json(
            ['message' => "Successfully created the user {$user->name}"],
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  $user
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        Gate::authorize('view', User::class);
        try {
            $user = $this->user_repo->findOrFail($user);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => "The user {$user} does not exist"
            ], 404);
        }
        return new UserResource($user);
    }

    /**
     * Display the specified resource by Email.
     *
     * @param  $email
     * @return \Illuminate\Http\Response
     */
    public function showByEmail($email)
    {
        Gate::authorize('view', User::class);
        try {
            $user = $this->user_repo->findByEmailOrFail($email);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => "The user {$email} does not exist"
            ], 404);
        }
        return new UserResource($user);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\StoreUserRequest $request
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $user_id)
    {
        try {
            $user = $this->user_repo->update($user_id, $request->all());
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => "The user {$user_id} could not be found"
            ], 404);
        } catch (UserNotUpdatedException $e) {
            return response()->json([
                'error' => "The user {$user_id} could not be updated"
            ], 500);
        }

        return response()->json(
            ['message' => "Successfully updated the user {$user->name}"]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Http\Requests\DeleteUserRequest $request
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUserRequest $request, $user_id)
    {
        try {
            $this->user_repo->delete($user_id);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => "The user {$user_id} could not be found"
            ], 404);
        } catch (UserNotDeletedException $e) {
            return response()->json([
                'error' => "The user {$user_id} could not be delete"
            ], 500);
        }

        return response()->json(
            ['message' => "Successfully deleted the user {$user_id}"],
            204
        );
    }


    /**
     * Grant a role to an user
     * 
     * @param  App\Http\Requests\GrantRoleToUserRequest $request
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function grantRole(GrantRoleToUserRequest $request, $user_id)
    {
        $role = $request->input('role');

        try {
            $user = $this->user_repo->findOrFail($user_id);
            $user->grantRole($role);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => "The user {$user_id} does not exist"
            ], 404);
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$role} does not exist"
            ], 404);
        }

        return response()->json(
            ['message' => "Successfully granted the role {$role} to the user {$user->name}"]
        );
    }

    /**
     * Revoke a role to an user
     *
     * @param  App\Http\Requests\RevokeRoleToUserRequest $request
     * @param  $user_id
     * @return \Illuminate\Http\Response
     */
    public function revokeRole(RevokeRoleToUserRequest $request, $user_id)
    {
        $role = $request->input('role');

        try {
            $user = $this->user_repo->findOrFail($user_id);
            $user->revokeRole($role);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'error' => "The user {$user_id} does not exist"
            ], 404);
        } catch (RoleNotFoundException $e) {
            return response()->json([
                'error' => "The role {$role} does not exist"
            ], 404);
        }

        return response()->json(
            ['message' => "Successfully revoked the role {$role} to the user {$user->name}"]
        );
    }
}
