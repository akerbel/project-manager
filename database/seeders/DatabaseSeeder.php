<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user.
        $admin = new User([
            'name' => 'Admin User',
            'role' => User::ADMIN_ROLE,
        ]);
        $admin->setEmail('admin@example.com');
        $admin->setPassword('12345678');
        $admin->markEmailAsVerified();
        $admin->save();

        // Create simple user.
        $user = new User([
            'name' => 'Simple User',
            'role' => User::USER_ROLE,
        ]);
        $user->setEmail('user@example.com');
        $user->setPassword('12345678');
        $user->markEmailAsVerified();
        $user->save();

        // Create some categories.
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category([
                'name' => 'Category ' . $i,
                'description' => 'Description ' . $i,
            ]);
            $category->save();
        }
    }
}
