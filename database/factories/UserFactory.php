<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'email_verified_at' => now(),

            'name' => fake()->name(),
            'user_id' => fake()->unique()->safeEmail(), // Membuat ID seperti EMP123
            'role' => fake()->randomElement(['manager', 'karyawan']),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'divisi' => fake()->randomElement(['Management', 'IT', 'HR']),
            'lokasi_kerja' => fake()->randomElement(['Bandung', 'Jakarta']),
            'tanggal_masuk' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
