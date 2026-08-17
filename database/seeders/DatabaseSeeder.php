<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Konekt\Acl\Models\RoleProxy;
use Konekt\User\Models\UserType;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(StorefrontCatalogSeeder::class);

        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'type' => UserType::ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $role = RoleProxy::where('name', 'admin')->first();
        if ($role) {
            $user->assignRole('admin');
        }
    }
}
