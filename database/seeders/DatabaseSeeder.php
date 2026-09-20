<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['slug'=>'admin.access','name'=>'Admin Access','description'=>'Access admin-only operations.'],
            ['slug'=>'users.view','name'=>'View Users','description'=>'View agency users.'],
            ['slug'=>'projects.manage','name'=>'Manage Projects','description'=>'Create and manage agency projects.'],
            ['slug'=>'tasks.manage','name'=>'Manage Tasks','description'=>'Manage team tasks.'],
            ['slug'=>'tasks.view','name'=>'View Tasks','description'=>'View assigned tasks.'],
            ['slug'=>'projects.view','name'=>'View Projects','description'=>'View client projects.'],
            ['slug'=>'requests.create','name'=>'Create Requests','description'=>'Create client service requests.'],
        ];
        foreach ($permissions as $permission) Permission::updateOrCreate(['slug'=>$permission['slug']], $permission);

        $map = [
            'admin' => array_column($permissions, 'slug'),
            'team_member' => ['tasks.view','tasks.manage','projects.view'],
            'client' => ['projects.view','requests.create'],
        ];
        foreach ($map as $role => $slugs) {
            foreach ($slugs as $slug) {
                $permission = Permission::where('slug',$slug)->first();
                DB::table('role_permissions')->updateOrInsert(['role'=>$role,'permission_id'=>$permission->id]);
            }
        }

        $users = [
            ['name'=>'Biswas IT Admin','email'=>'admin@biswasit.test','password'=>'password','role'=>'admin'],
            ['name'=>'Team Member','email'=>'team@biswasit.test','password'=>'password','role'=>'team_member'],
            ['name'=>'Client User','email'=>'client@biswasit.test','password'=>'password','role'=>'client'],
        ];
        foreach ($users as $user) User::updateOrCreate(['email'=>$user['email']], [
            'name'=>$user['name'], 'password'=>Hash::make($user['password']), 'role'=>$user['role'],
        ]);
    }
}
