<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactGroupSeeder extends Seeder
{
    public function run(): void
    {
        $existing = DB::table('contact_groups')->count();
        if ($existing === 0) {
            DB::table('contact_groups')->insert([
                ['name' => 'Work'],
                ['name' => 'Friends'],
                ['name' => 'Family'],
            ]);
        }
    }
}
