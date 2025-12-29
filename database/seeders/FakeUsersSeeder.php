<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;
use Spatie\Permission\Models\Role;

class FakeUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates random fake users with different roles.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // Create 5 Admin users
        for ($i = 1; $i <= 5; $i++) {
            $idNumber = 'AD' . str_pad($i + 100, 4, '0', STR_PAD_LEFT);
            $admin = User::firstOrCreate(
                ['id_number' => $idNumber],
                [
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => Hash::make('password123'),
                    'photo_path' => null,
                    'status' => $faker->randomElement([0, 1]),
                    'email_verified_at' => $faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                    'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'updated_at' => now(),
                ]
            );
            if ($admin->wasRecentlyCreated && !$admin->hasRole($adminRole)) {
                $admin->assignRole($adminRole);
            }
        }

        // Create 20 Teacher users
        for ($i = 1; $i <= 20; $i++) {
            $idNumber = 'TE' . str_pad($i + 100, 4, '0', STR_PAD_LEFT);
            $teacher = User::firstOrCreate(
                ['id_number' => $idNumber],
                [
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => Hash::make('password123'),
                    'photo_path' => null,
                    'status' => $faker->randomElement([0, 1, 1, 1]),
                    'email_verified_at' => $faker->optional(0.9)->dateTimeBetween('-1 year', 'now'),
                    'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'updated_at' => now(),
                ]
            );
            if ($teacher->wasRecentlyCreated && !$teacher->hasRole($teacherRole)) {
                $teacher->assignRole($teacherRole);
            }
        }

        // Create 100 Student users
        for ($i = 1; $i <= 100; $i++) {
            $idNumber = 'ST' . str_pad($i + 100, 4, '0', STR_PAD_LEFT);
            $student = User::firstOrCreate(
                ['id_number' => $idNumber],
                [
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => Hash::make('password123'),
                    'photo_path' => null,
                    'status' => $faker->randomElement([0, 1, 1, 1, 1]),
                    'email_verified_at' => $faker->optional(0.7)->dateTimeBetween('-1 year', 'now'),
                    'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                    'updated_at' => now(),
                ]
            );
            if ($student->wasRecentlyCreated && !$student->hasRole($studentRole)) {
                $student->assignRole($studentRole);
            }
        }
    }
}
