<?php

namespace Database\Seeders;

use App\Models\Role;
use Faker\Generator as Faker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $superadmin = User::firstOrCreate([
            'name' => 'Super Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('12345678'),
            'dob' => Carbon::parse('2000-01-01'),
            'gender' => 'M',
            'dni' => $faker->randomNumber(7),
            'address' => $faker->address,
            'country' => $faker->country,
            'phone' => $faker->phoneNumber
        ]);

        $superadmin->grantRole(Role::$SUPERADMIN);
    }
}
