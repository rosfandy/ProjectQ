<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'todo'],
            ['name' => 'in_progress'],
            ['name' => 'done'],
        ];

        foreach ($data as $role) {
            \App\Models\Status::create($role);
        }
    }
}
