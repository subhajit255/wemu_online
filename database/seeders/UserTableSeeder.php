<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Request $request): void
    {
        $faker = Faker::create();

        // Super Admin User
        $permissions = Permission::all();
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $superAdminUser = User::where('email', 'super.admin@kydir.com')->first();

        if(empty($superAdminUser)){
            $superAdminUser = new User();
            $superAdminUser->uuid = $faker->uuid;
            $superAdminUser->name = 'Super Admin';
            $superAdminUser->username = 'SuperAdmin';
            $superAdminUser->user_type = 1;
            $superAdminUser->email = 'super.admin@kydir.com';
            $superAdminUser->phone_code = 91;
            $superAdminUser->mobile_number = 8906226970;
            $superAdminUser->password = bcrypt('admin@123');
            $superAdminUser->registration_ip = $request->getClientIp();
            $superAdminUser->is_active = 1;
            $superAdminUser->save();
        }

        $superAdminUser->roles()->sync($superAdminRole);
        $superAdminUser->permissions()->sync($permissions);
        $superAdminRole->permissions()->sync($permissions);

        // Sub Admin User
        // $subAdminRole = Role::where('slug', 'sub-admin')->first();
        // $subAdminUser = new User();
        // $subAdminUser->uuid = $faker->uuid;
        // $subAdminUser->name = 'Sub Admin';
        // $subAdminUser->username = 'SubAdmin';
        // $subAdminUser->user_type = 1;
        // $subAdminUser->email = 'subadmin@example.com';
        // $subAdminUser->mobile_number = 1234567890;
        // $subAdminUser->password = bcrypt('subadmin@123');
        // $subAdminUser->registration_ip = $request->getClientIp();
        // $subAdminUser->is_active = 1;
        // $subAdminUser->save();
        // $subAdminUser->roles()->attach($subAdminRole);
    }
}
