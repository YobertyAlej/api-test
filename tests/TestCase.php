<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $faker;
    public $super_admin;
    public $user_without_roles;

    public function setUp(): void
    {
        parent::setUp();

        $role_repo = $this->app->make(RoleRepository::class);

        $this->faker = \Faker\Factory::create();

        $this->super_admin_role = $role_repo->getSuperAdmin();
        $this->super_admin = $this->super_admin_role->users->first();

        $this->user_without_roles = User::factory()->create();
    }

    private function setupUser(Role $role, $retry = 0)
    {
        try {
            $user = User::factory()->create(
                [
                    'name' => $this->faker->name(),
                    'email' => $this->faker->email,
                    'password' => Hash::make('12345678'),
                    'dob' => Carbon::parse('2000-01-01'),
                    'gender' => 'M',
                    'dni' => $this->faker->randomNumber(7),
                    'address' => $this->faker->address,
                    'country' => $this->faker->country,
                    'phone' => $this->faker->phoneNumber
                ]
            );

            $user->grantRole($role);

        } catch (Exception $ex) {
            if ($retry < 2) {
                return $this->setupUser($role, $retry + 1);
            }

            throw $ex;
        }
    }
}
