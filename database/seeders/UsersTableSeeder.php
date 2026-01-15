<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating Permissions (matching gate checks in views)
        $userAccess = Permission::firstOrCreate(['name' => 'user_access']);
        $userView = Permission::firstOrCreate(['name' => 'view users']);
        $userEdit = Permission::firstOrCreate(['name' => 'edit users']);
        $userCreate = Permission::firstOrCreate(['name' => 'create users']);
        $userDelete = Permission::firstOrCreate(['name' => 'delete users']);

        $roleAccess = Permission::firstOrCreate(['name' => 'role_access']);
        $categoryAccess = Permission::firstOrCreate(['name' => 'category_access']);
        $tagAccess = Permission::firstOrCreate(['name' => 'tag_access']);

        $materialView = Permission::firstOrCreate(['name' => 'view materials']);
        $materialCreate = Permission::firstOrCreate(['name' => 'create materials']);
        $materialEdit = Permission::firstOrCreate(['name' => 'edit materials']);
        $materialDelete = Permission::firstOrCreate(['name' => 'delete materials']);

        // Creating Roles
        $roleEducator = Role::firstOrCreate(['name' => 'Educator']);
        $roleLearner = Role::firstOrCreate(['name' => 'Learner']);

        // Assigning Permissions to Roles
        // Educator gets user management and material permissions
        $roleEducator->givePermissionTo([
            $userAccess,
            $userView,
            $userEdit,
            $userCreate,
            $userDelete,
            $materialView,
            $materialCreate,
            $materialEdit,
            $materialDelete
        ]);

        // Learner gets view-only permissions
        $roleLearner->givePermissionTo([
            $userView,
            $materialView
        ]);

        // Creating Educator User
        $educator = User::firstOrCreate(
            ['email' => 'educator@demo.com'],
            [
                'name' => 'Educator User',
                'id_number' => 'ED001',
                'password' => Hash::make('educatorpassword'),
                'status' => 1,
            ]
        );
        if (!$educator->hasRole($roleEducator)) {
            $educator->assignRole($roleEducator);
        }

        // Creating Learner User
        $learner = User::firstOrCreate(
            ['email' => 'learner@demo.com'],
            [
                'name' => 'Learner User',
                'id_number' => 'LR001',
                'password' => Hash::make('learnerpassword'),
                'status' => 1,
            ]
        );
        if (!$learner->hasRole($roleLearner)) {
            $learner->assignRole($roleLearner);
        }
    }
}
