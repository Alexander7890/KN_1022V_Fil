<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('contacts')->count() > 0) {
            return;
        }

        $groupIds = DB::table('contact_groups')->pluck('id', 'name');

        DB::table('contacts')->insert([
            [
                'name'      => 'John Doe',
                'email'     => 'john@example.com',
                'phone'     => '+380501112233',
                'note'      => 'Colleague from work.',
                'group_id'  => $groupIds['Work'] ?? null,
            ],
            [
                'name'      => 'Jane Smith',
                'email'     => 'jane@example.com',
                'phone'     => '+380671234567',
                'note'      => 'Close friend.',
                'group_id'  => $groupIds['Friends'] ?? null,
            ],
            [
                'name'      => 'Mom',
                'email'     => 'mom@example.com',
                'phone'     => '+380931234567',
                'note'      => 'Family contact.',
                'group_id'  => $groupIds['Family'] ?? null,
            ],
        ]);
    }
}
