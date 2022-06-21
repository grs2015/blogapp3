<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'mobile' => $this->faker->phoneNumber(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * The User is admin
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin()
    {
        return $this->state(function () {
            return [
                'role' => User::ADMIN_USER
            ];
        });
    }

    /**
     * The User is author
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function author()
    {
        return $this->state(function () {
            return [
                'role' => User::AUTHOR_USER
            ];
        });
    }

    /**
     * The User is regular
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function regular()
    {
        return $this->state(function () {
            return [
                'role' => User::REGULAR_USER
            ];
        });
    }
}
