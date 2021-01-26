<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Faker instance.
     *
     * @var Faker
     */
    private $faker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();

        self::defaultUsers();
        self::generateUsers();
    }

    /**
     * Generates the default users.
     */
    public function defaultUsers()
    {
        User::create([
            'first_name' => 'Tiber',
            'last_name' => 'Septim',
            'email' => 'talos@skyrim.net',
            'password' => Hash::make('lazaro93'),
            'phone' => '123',
            'date_of_birth' => '2011-11-11',
            'api_token' => 'tiber',
            'type' => User::ADMIN_USER,
        ]);
    }

    /**
     * Generates random users.
     */
    public function generateUsers()
    {
        $count = [
            'regular' => 0,
            'admin' => 0,
            'doorman' => 0,
        ];

        for ($i = 0; $i < 50; $i++) {
            $type = $this->faker->randomElement([User::ADMIN_USER, User::REGULAR_USER, User::DOORMAN_USER]);

            switch ($type) {
                case User::ADMIN_USER:
                    $first_name = 'admin';
                    break;
                case User::DOORMAN_USER:
                    $first_name = 'doorman';
                    break;
                default:
                    $first_name = 'regular';
                    break;
            }

            $count[$first_name]++;

            User::create([
                'first_name' => $first_name,
                'last_name' => $count[$first_name],
                'email' => $first_name . $count[$first_name] . '@a.com',
                'password' => Hash::make('1'),
                'phone' => '123',
                'date_of_birth' => '2011-11-11',
                'api_token' => User::generateApiToken(),
                'type' => $type,
            ]);
        }
    }
}
