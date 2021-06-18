<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::index
     */
    public function iCanListAllTheUsers()
    {
        User::factory()->count(28)->create();

        $token = $this->createToken($this->super_admin);
        $response = $this->doGet(route('users.index'), $token);

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJson(['meta' => ['total' => 30]]);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::index
     */
    public function iCanNotListAllTheUsersUnauthorized()
    {
        User::factory()->count(28)->create();

        $token = $this->createToken($this->user_without_roles);
        $response = $this->doGet(route('users.index'), $token);

        $response->assertStatus(403);
        $response->assertSee('This action is unauthorized.');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::index
     */
    public function iCanSeeTheDetailsOfAnUser()
    {
        $user = User::factory()->create();

        $token = $this->createToken($this->super_admin);
        $response = $this->doGet(route('users.show', $user->id), $token);

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::showByEmail
     */
    public function iCanSeeTheDetailsOfAnUserByEmail()
    {
        $user = User::factory()->create();

        $token = $this->createToken($this->super_admin);
        $response = $this->doGet(route('users.byEmail', $user->email), $token);

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::showByEmail
     */
    public function iCanNotSeeTheDetailsOfAnUserThatNotExistByEmail()
    {
        $email = $this->faker->email;
        $token = $this->createToken($this->super_admin);
        $response = $this->doGet(route('users.byEmail', $email), $token);

        $response->assertStatus(404);
        $response->assertSee("The user {$email} does not exist");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::show
     */
    public function iCanNotSeeTheDetailsOfAnUserThatNotExist()
    {
        $user = User::latest()->first();
        $user_id = $user->id + 1;

        $token = $this->createToken($this->super_admin);
        $response = $this->doGet(route('users.show', $user_id), $token);

        $response->assertStatus(404);
        $response->assertSee("The user {$user_id} does not exist");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::show
     */
    public function iCanNotSeeTheDetailsOfAnUserUnauthorized()
    {
        $user = User::factory()->create();

        $token = $this->createToken($this->user_without_roles);
        $response = $this->doGet(route('users.show', $user->id), $token);

        $response->assertStatus(403);
        $response->assertSee('This action is unauthorized.');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::store
     * @covers \App\Http\Requests\StoreUserRequest::authorize
     * @covers \App\Http\Requests\StoreUserRequest::rules
     */
    public function iCanCreateNewUsers()
    {
        $user = User::factory()->make();
        $payload = $this->createPayload($user);
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.store'), $payload, $token);

        $response->assertStatus(201);
        $response->assertSee('Successfully created the user');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::store
     * @covers \App\Http\Requests\StoreUserRequest::authorize
     * @covers \App\Http\Requests\StoreUserRequest::rules
     */
    public function iCanNotCreateDuplicateUsers()
    {
        $created_user = User::factory()->create();
        $user = User::factory()->make(['email' => $created_user->email]);
        $payload = $this->createPayload($user);
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.store'), $payload, $token);

        $response->assertStatus(422);
        $response->assertSee('The email has already been taken.');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::store
     * @covers \App\Http\Requests\StoreUserRequest::authorize
     * @covers \App\Http\Requests\StoreUserRequest::rules
     */
    public function iCanNotCreateUsersUnauthorized()
    {
        $user = User::factory()->make();
        $payload = $this->createPayload($user);
        $token = $this->createToken($this->user_without_roles);

        $response = $this->doPost(route('users.store'), $payload, $token);

        $response->assertStatus(403);
        $response->assertSee('This action is unauthorized.');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::update
     * @covers \App\Http\Requests\UpdateUserRequest::authorize
     * @covers \App\Http\Requests\UpdateUserRequest::rules
     */
    public function iCanUpdateUsers()
    {
        $created_user = User::factory()->create();
        $user = User::factory()->make();
        $payload = ['name' => $user->name];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPut(route('users.update', $created_user->id), $payload, $token);

        $response->assertStatus(200);
        $response->assertSee('Successfully updated the user');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::update
     * @covers \App\Http\Requests\UpdateUserRequest::authorize
     * @covers \App\Http\Requests\UpdateUserRequest::rules
     */
    public function iCanNotUpdateUsersUnauthorized()
    {
        $created_user = User::factory()->create();
        $user = User::factory()->make();
        $payload = ['name' => $user->name];
        $token = $this->createToken($this->user_without_roles);

        $response = $this->doPut(route('users.update', $created_user->id), $payload, $token);

        $response->assertStatus(403);
        $response->assertSee('This action is unauthorized.');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::update
     */
    public function iCanNotUpdateAnUserThatNotExist()
    {
        $user = User::latest()->first();
        $user_id = $user->id + 1;

        $token = $this->createToken($this->super_admin);
        $response = $this->doPut(route('users.update', $user_id), [], $token);

        $response->assertStatus(404);
        $response->assertSee("The user {$user_id} could not be found");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::destroy
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanDeleteUsers()
    {
        $user = User::factory()->create();
        $token = $this->createToken($this->super_admin);

        $response = $this->doDelete(route('users.destroy', $user->id), $token);

        $response->assertStatus(204);
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::destroy
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotDeleteUsersUnautorized()
    {
        $user = User::factory()->create();
        $token = $this->createToken($this->user_without_roles);

        $response = $this->doDelete(route('users.destroy', $user->id), $token);

        $response->assertStatus(403);
        $response->assertSee('This action is unauthorized.');
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::destroy
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotDeleteUsersThatNotExist()
    {
        $user = User::latest()->first();
        $user_id = $user->id + 1;
        $token = $this->createToken($this->super_admin);

        $response = $this->doDelete(route('users.destroy', $user_id), $token);

        $response->assertStatus(404);
        $response->assertSee("The user {$user_id} could not be found");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::grantRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanGrantRoleToUser()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $payload = ['role' => $role->name];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.grantRole', $user->id), $payload, $token);

        $response->assertStatus(200);
        $response->assertSee("Successfully granted the role {$role->name} to the user {$user->name}");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::grantRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotGrantARoleToAUserThatNotExist()
    {
        $user = User::latest()->first();
        $user_id = $user->id + 1;
        $payload = ['role' => Str::random()];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.grantRole', $user_id), $payload, $token);

        $response->assertStatus(404);
        $response->assertSee("The user {$user_id} does not exist");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::grantRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotGrantARoleThatNotExist()
    {
        $user = User::latest()->first();
        $user_id = $user->id;
        $role = Str::random();
        $payload = ['role' => $role];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.grantRole', $user_id), $payload, $token);

        $response->assertStatus(404);
        $response->assertSee("The role {$role} does not exist");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::grantRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotGrantARoleToAUserThatWithEmptyPayload()
    {
        $user = User::latest()->first();
        $user_id = $user->id;
        $payload = [];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.grantRole', $user_id), $payload, $token);

        $response->assertStatus(422);
        $response->assertSee("The role field is required");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::grantRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotGrantARoleToAUserUnauthorized()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $payload = ['role' => $role->name];
        $token = $this->createToken($this->user_without_roles);

        $response = $this->doPost(route('users.grantRole', $user->id), $payload, $token);

        $response->assertStatus(403);
        $response->assertSee("This action is unauthorized.");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::grantRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanRevokeRoleToUser()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->grantRole($role->name);

        $payload = ['role' => $role->name];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.revokeRole', $user->id), $payload, $token);

        $response->assertStatus(200);
        $response->assertSee("Successfully revoked the role {$role->name} to the user {$user->name}");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::revokeRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotRevokeARoleToAUserThatNotExist()
    {
        $user = User::latest()->first();
        $user_id = $user->id + 1;
        $payload = ['role' => Str::random()];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.revokeRole', $user_id), $payload, $token);

        $response->assertStatus(404);
        $response->assertSee("The user {$user_id} does not exist");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::revokeRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotRevokeARoleThatNotExist()
    {
        $user = User::latest()->first();
        $user_id = $user->id;
        $role = Str::random();
        $payload = ['role' => $role];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.revokeRole', $user_id), $payload, $token);

        $response->assertStatus(404);
        $response->assertSee("The role {$role} does not exist");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::revokeRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotRevokeARoleToAUserUnauthorized()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $payload = ['role' => $role->name];
        $token = $this->createToken($this->user_without_roles);

        $response = $this->doPost(route('users.revokeRole', $user->id), $payload, $token);

        $response->assertStatus(403);
        $response->assertSee("This action is unauthorized.");
    }

    /**
     * @test
     * @covers \App\Http\Controllers\Api\UserControllerTest::__construct
     * @covers \App\Http\Controllers\Api\UserControllerTest::revokeRole
     * @covers \App\Http\Requests\DeleteUserRequest::authorize
     * @covers \App\Http\Requests\DeleteUserRequest::rules
     */
    public function iCanNotRevokeARoleToAUserThatWithEmptyPayload()
    {
        $user = User::latest()->first();
        $user_id = $user->id;
        $payload = [];
        $token = $this->createToken($this->super_admin);

        $response = $this->doPost(route('users.revokeRole', $user_id), $payload, $token);

        $response->assertStatus(422);
        $response->assertSee("The role field is required");
    }

    private function createPayload($user)
    {
        $password = $user->password;
        $dob = $user->dob->format('Y-m-d');
        $payload = $user->toArray();
        $payload['password'] = $password;
        $payload['dob'] = $dob;

        return $payload;
    }

    private function createToken($user, $name = '')
    {
        if (!$name) {
            $name = $this->faker->name;
        }

        $user->tokens()->delete();
        return $user->createToken($name)->plainTextToken;
    }

    private function doPost($route, $data, $token)
    {
        return $this->postJson(
            $route,
            $data,
            ['Authorization' => 'Bearer ' . $token,]
        );
    }

    private function doGet($route, $token)
    {
        return $this->getJson(
            $route,
            ['Authorization' => 'Bearer ' . $token,]
        );
    }

    private function doPut($route, $data, $token)
    {
        return $this->putJson(
            $route,
            $data,
            ['Authorization' => 'Bearer ' . $token,]
        );
    }

    private function doDelete($route, $token)
    {
        return $this->deleteJson(
            $route,
            [],
            ['Authorization' => 'Bearer ' . $token,]
        );
    }
}
