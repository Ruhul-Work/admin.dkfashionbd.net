<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\backend\FiscalYear;

class FiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!FiscalYear::exists()) {
            FiscalYear::create([
                'name'       => date('Y').'-'.(date('Y')+1),
                'start_date' => date('Y').'-01-01',
                'end_date'   => date('Y').'-12-31',
                'is_active'  => 1,
            ]);
        }
    }
}
