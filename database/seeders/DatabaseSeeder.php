<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@mst.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create default settings for the admin user
        Setting::forUser($user->id);

        $this->command->info('Default admin user created: admin@mst.com / password');
        $this->command->info('Default settings created for admin user');
        
        // Seed tech services
        $this->call(TechServicesSeeder::class);
        
        // Seed default chart of accounts
        $this->call(ChartOfAccountsSeeder::class);
        
        // Seed IT inventory (products, suppliers, purchases, stock)
        $this->call(ItInventorySeeder::class);
        
        // Seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
