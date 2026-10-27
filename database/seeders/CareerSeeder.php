<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Center;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    /**
     * Representative technical careers offered by each center.
     *
     * @var array<string, array<int, string>>
     */
    private const array CAREERS = [
        'CT-MN-001' => ['Técnico en Informática', 'Técnico en Desarrollo de Software', 'Técnico en Redes y Telecomunicaciones'],
        'CT-MN-002' => ['Técnico en Electricidad', 'Técnico en Mecánica Automotriz', 'Técnico en Refrigeración y Climatización'],
        'CT-MN-003' => ['Técnico en Contabilidad', 'Técnico en Administración', 'Técnico en Mercadotecnia'],
        'CT-MN-004' => ['Técnico en Hotelería y Turismo', 'Técnico en Gastronomía'],
        'CT-MN-005' => ['Técnico en Enfermería', 'Técnico en Farmacia', 'Técnico en Educación Infantil'],
        'CT-MN-006' => ['Técnico en Construcción Civil', 'Técnico en Topografía', 'Técnico en Electricidad'],

        'CT-MS-001' => ['Técnico en Informática', 'Técnico en Contabilidad'],
        'CT-MS-002' => ['Técnico en Artesanía', 'Técnico en Confección Industrial'],

        'CT-GR-001' => ['Técnico en Hotelería y Turismo', 'Técnico en Gastronomía'],
        'CT-GR-002' => ['Técnico en Agropecuaria', 'Técnico en Contabilidad'],

        'CT-CA-001' => ['Técnico en Informática', 'Técnico en Contabilidad', 'Técnico en Mercadotecnia'],
        'CT-CA-002' => ['Técnico en Mecánica Automotriz', 'Técnico en Electricidad'],

        'CT-RI-001' => ['Técnico en Agropecuaria', 'Técnico en Contabilidad'],
        'CT-RI-002' => ['Técnico en Hotelería y Turismo', 'Técnico en Gastronomía'],

        'CT-LE-001' => ['Técnico en Informática', 'Técnico en Enfermería', 'Técnico en Contabilidad'],
        'CT-LE-002' => ['Técnico en Electricidad', 'Técnico en Soldadura'],
        'CT-LE-003' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],

        'CT-CI-001' => ['Técnico en Informática', 'Técnico en Mecánica Automotriz', 'Técnico en Contabilidad'],
        'CT-CI-002' => ['Técnico en Electricidad', 'Técnico en Refrigeración y Climatización'],
        'CT-CI-003' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],

        'CT-ES-001' => ['Técnico en Informática', 'Técnico en Contabilidad', 'Técnico en Enfermería'],
        'CT-ES-002' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],

        'CT-MD-001' => ['Técnico en Informática', 'Técnico en Contabilidad'],
        'CT-MD-002' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],

        'CT-NS-001' => ['Técnico en Informática', 'Técnico en Enfermería', 'Técnico en Contabilidad'],
        'CT-NS-002' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],
        'CT-NS-003' => ['Técnico en Agropecuaria', 'Técnico en Electricidad'],

        'CT-JI-001' => ['Técnico en Informática', 'Técnico en Contabilidad', 'Técnico en Agropecuaria'],
        'CT-JI-002' => ['Técnico en Producción Agrícola', 'Técnico en Electricidad'],

        'CT-MT-001' => ['Técnico en Informática', 'Técnico en Contabilidad', 'Técnico en Mecánica Automotriz'],
        'CT-MT-002' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],
        'CT-MT-003' => ['Técnico en Agropecuaria', 'Técnico en Electricidad'],

        'CT-BO-001' => ['Técnico en Informática', 'Técnico en Contabilidad', 'Técnico en Agropecuaria'],
        'CT-BO-002' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],

        'CT-CO-001' => ['Técnico en Informática', 'Técnico en Enfermería', 'Técnico en Contabilidad'],
        'CT-CO-002' => ['Técnico en Agropecuaria', 'Técnico en Electricidad'],

        'CT-SJ-001' => ['Técnico en Informática', 'Técnico en Contabilidad', 'Técnico en Agropecuaria'],
        'CT-SJ-002' => ['Técnico en Hotelería y Turismo', 'Técnico en Producción Agrícola'],

        'CT-AN-001' => ['Técnico en Informática', 'Técnico en Enfermería', 'Técnico en Contabilidad'],
        'CT-AN-002' => ['Técnico en Agropecuaria', 'Técnico en Electricidad'],
        'CT-AN-003' => ['Técnico en Producción Agrícola', 'Técnico en Electricidad'],

        'CT-AS-001' => ['Técnico en Informática', 'Técnico en Contabilidad', 'Técnico en Enfermería'],
        'CT-AS-002' => ['Técnico en Agropecuaria', 'Técnico en Producción Agrícola'],
        'CT-AS-003' => ['Técnico en Hotelería y Turismo', 'Técnico en Gastronomía'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centerIds = Center::query()->pluck('id', 'code');

        foreach (self::CAREERS as $centerCode => $careers) {
            $centerId = $centerIds[$centerCode];

            if ($centerId === null) {
                continue;
            }

            foreach ($careers as $name) {
                Career::firstOrCreate([
                    'center_id' => $centerId,
                    'name' => $name,
                ]);
            }
        }
    }
}
