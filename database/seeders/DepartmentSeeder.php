<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Official ISO 3166-2:NI codes for the 15 departments and 2 autonomous regions.
     *
     * @var array<int, array{name: string, code: string}>
     */
    private const array DEPARTMENTS = [
        ['name' => 'Managua', 'code' => 'NI-MN'],
        ['name' => 'León', 'code' => 'NI-LE'],
        ['name' => 'Chinandega', 'code' => 'NI-CI'],
        ['name' => 'Granada', 'code' => 'NI-GR'],
        ['name' => 'Masaya', 'code' => 'NI-MS'],
        ['name' => 'Carazo', 'code' => 'NI-CA'],
        ['name' => 'Rivas', 'code' => 'NI-RI'],
        ['name' => 'Estelí', 'code' => 'NI-ES'],
        ['name' => 'Madriz', 'code' => 'NI-MD'],
        ['name' => 'Nueva Segovia', 'code' => 'NI-NS'],
        ['name' => 'Jinotega', 'code' => 'NI-JI'],
        ['name' => 'Matagalpa', 'code' => 'NI-MT'],
        ['name' => 'Boaco', 'code' => 'NI-BO'],
        ['name' => 'Chontales', 'code' => 'NI-CO'],
        ['name' => 'Río San Juan', 'code' => 'NI-SJ'],
        ['name' => 'Costa Caribe Norte', 'code' => 'NI-AN'],
        ['name' => 'Costa Caribe Sur', 'code' => 'NI-AS'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::upsert(self::DEPARTMENTS, ['code'], ['name']);
    }
}
