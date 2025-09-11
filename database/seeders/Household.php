<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\Household as ModelsHousehold;
use App\Models\Municipality;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Household extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=1; $i<=3; $i++){
            Service::create([
                'name' => fake()->word(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        for ($i = 1; $i <= 10; $i++) {
            // Pick a random municipality
            $municipality = Municipality::inRandomOrder()->first();

            // Pick a random barangay from that municipality
            $barangay = Barangay::where('municipality_id', $municipality->id)->inRandomOrder()->first();

            // If no barangay found, skip this iteration
            if (!$barangay) {
                continue;
            }

            $household = ModelsHousehold::create([
                'municipality' => $municipality->code,
                'barangay' => $barangay->code,
                'purok' => 'Purok ' . fake()->numberBetween(1, 10),
                'user_id' => User::inRandomOrder()->first()->id,
                'address' => $municipality->name . ', ' . $barangay->name . ', Purok ' . fake()->numberBetween(1, 10) . ', Street ' . fake()->numberBetween(1, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            for ($j = 1; $j <= 3; $j++) {
                $household->members()->create([
                    'household_id' => $household->id,
                    'role' => $j === 1 ? 'Leader' : fake()->randomElement(['Spouse', 'Child', 'Relative']),
                    'first_name' => fake()->firstName(),
                    'middle_name' => fake()->firstName(),
                    'surname' => fake()->lastName(),
                    'suffix' => fake()->randomElement(['Jr.', 'Sr.', 'III', null]),
                    'birth_date' => fake()->date(),
                    'sex' => fake()->randomElement(['male', 'female']),
                    'precinct_no' => fake()->numberBetween(1, 100),
                    'cluster_no' => fake()->numberBetween(1, 50),
                    'is_leader' => $j === 1,
                ]);
            }
        }
    }
}
