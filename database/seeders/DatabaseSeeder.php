<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents; 

    public function run(): void
    {
         // Data Student Biasa
Student::create([
    'studentID' => '2024991653',
    'name' => 'Zulhilmi Hisyam',
    'email' => 'hisyamjer18@gmail.com',
    'password' =>  Hash::make('password'),   
    'phone_number' => '0123456789',
]);

// Data Admin
Admin::create([
    'email' => 'admin@example.com',
    'password' =>  Hash::make('password')   ,
]);
    }
}
