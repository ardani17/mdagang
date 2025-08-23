<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks for seeding
        \DB::statement('SET CONSTRAINTS ALL DEFERRED');

        // Call seeders in order of dependencies
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            // ManufacturingSeeder::class, // Commented out - needs fixing for column mismatch
            // Additional seeders can be added here as needed
        ]);

        // Re-enable foreign key checks
        \DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $this->command->info('Database seeding completed successfully!');
    }
}
