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
        // Creating Permissions
        $permission1 = Permission::create(['name' => 'view users']);
        $permission2 = Permission::create(['name' => 'edit users']);
        $permission3 = Permission::create(['name' => 'create users']);
        $permission4 = Permission::create(['name' => 'delete users']);
        $permission5 = Permission::create(['name' => 'view materials']);
        $permission6 = Permission::create(['name' => 'create materials']);
        $permission7 = Permission::create(['name' => 'edit materials']);
        $permission8 = Permission::create(['name' => 'delete materials']);

      

        // Creating Roles
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleTeacher = Role::create(['name' => 'Teacher']);
        $roleStudent = Role::create(['name' => 'Student']);

        // Assigning Permissions to Roles
        $roleAdmin->givePermissionTo([
            $permission1, $permission2, $permission3, $permission4, 
            $permission5, $permission6, $permission7, $permission8
        ]);

        $roleTeacher->givePermissionTo([
            $permission1, $permission5, $permission6, $permission7
        ]);

        $roleStudent->givePermissionTo([
            $permission1, $permission5
        ]);

        // Creating Admin User
        $admin = User::create(
            
        [
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('adminpassword'),
            'status' => 1,  // Active status
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Akmal Razelan',
            'email' => 'akmalraze@gmail.com',
            'password' => Hash::make('12345678'),
            'status' => 1,  // Active status
            'created_at' => now(),
            'updated_at' => now(),
        ]
    
    );
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
