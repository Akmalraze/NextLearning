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
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleTeacher = Role::firstOrCreate(['name' => 'Teacher']);
        $roleStudent = Role::firstOrCreate(['name' => 'Student']);

        // Assigning Permissions to Roles
        // Admin gets all permissions
        $roleAdmin->givePermissionTo([
            $userAccess,
            $userView,
            $userEdit,
            $userCreate,
            $userDelete,
            $roleAccess,
            $categoryAccess,
            $tagAccess,
            $materialView,
            $materialCreate,
            $materialEdit,
            $materialDelete
        ]);

        // Teacher gets limited permissions
        $roleTeacher->givePermissionTo([
            $userView,
            $materialView,
            $materialCreate,
            $materialEdit
        ]);

        // Student gets view-only permissions
        $roleStudent->givePermissionTo([
            $userView,
            $materialView
        ]);

        // Creating Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('adminpassword'),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $admin->assignRole($roleAdmin);

        // Creating Teacher User
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@demo.com',
            'password' => Hash::make('teacherpassword'),
            'status' => 1,  // Active status
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $teacher->assignRole($roleTeacher);

        // Creating Student User
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@demo.com',
            'password' => Hash::make('studentpassword'),
            'status' => 1,  // Active status
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $student->assignRole($roleStudent);
    }
}
