<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('absences')->insert([
            'name' => 'Godišnji odmor',
        ]);

        DB::table('absences')->insert([
            'name' => 'Bolovanje',
        ]);
    }
}
