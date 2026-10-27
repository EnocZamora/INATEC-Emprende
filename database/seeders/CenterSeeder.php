<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Municipality;
use Illuminate\Database\Seeder;
use RuntimeException;

class CenterSeeder extends Seeder
{
    /**
     * INATEC technology centers distributed across the national territory.
     *
     * Codes are internal catalog identifiers (CT-{department}-{sequence}).
     *
     * @var array<int, array{department: string, municipality: string, name: string, code: string, location: ?string}>
     */
    private const array CENTERS = [
        // Managua
        ['department' => 'NI-MN', 'municipality' => 'Managua', 'code' => 'CT-MN-001', 'name' => 'Centro Tecnológico de Managua', 'location' => 'Km 4 Carretera Norte, Managua'],
        ['department' => 'NI-MN', 'municipality' => 'Managua', 'code' => 'CT-MN-002', 'name' => 'Centro Tecnológico "Simón Bolívar"', 'location' => null],
        ['department' => 'NI-MN', 'municipality' => 'Managua', 'code' => 'CT-MN-003', 'name' => 'Centro Tecnológico "Carlos Fonseca Amador"', 'location' => null],
        ['department' => 'NI-MN', 'municipality' => 'Managua', 'code' => 'CT-MN-004', 'name' => 'Centro Tecnológico de Hotelería y Turismo', 'location' => null],
        ['department' => 'NI-MN', 'municipality' => 'Managua', 'code' => 'CT-MN-005', 'name' => 'Centro Tecnológico "Aldo Chavarría"', 'location' => null],
        ['department' => 'NI-MN', 'municipality' => 'Villa Carlos Fonseca', 'code' => 'CT-MN-006', 'name' => 'Centro Tecnológico "Villa Carlos Fonseca"', 'location' => null],

        // Masaya
        ['department' => 'NI-MS', 'municipality' => 'Masaya', 'code' => 'CT-MS-001', 'name' => 'Centro Tecnológico "Augusto César Sandino"', 'location' => null],
        ['department' => 'NI-MS', 'municipality' => 'Niquinohomo', 'code' => 'CT-MS-002', 'name' => 'Centro Tecnológico de Artesanía y Turismo', 'location' => null],

        // Granada
        ['department' => 'NI-GR', 'municipality' => 'Granada', 'code' => 'CT-GR-001', 'name' => 'Centro Tecnológico "Rafaela Herrera"', 'location' => null],
        ['department' => 'NI-GR', 'municipality' => 'Nandaime', 'code' => 'CT-GR-002', 'name' => 'Centro Tecnológico "Nandaime"', 'location' => null],

        // Carazo
        ['department' => 'NI-CA', 'municipality' => 'Jinotepe', 'code' => 'CT-CA-001', 'name' => 'Centro Tecnológico "Pedro Joaquín Chamorro Cardenal"', 'location' => null],
        ['department' => 'NI-CA', 'municipality' => 'Diriamba', 'code' => 'CT-CA-002', 'name' => 'Centro Tecnológico "Diriamba"', 'location' => null],

        // Rivas
        ['department' => 'NI-RI', 'municipality' => 'Rivas', 'code' => 'CT-RI-001', 'name' => 'Centro Tecnológico "Rivas"', 'location' => null],
        ['department' => 'NI-RI', 'municipality' => 'San Juan del Sur', 'code' => 'CT-RI-002', 'name' => 'Centro Tecnológico de Turismo "San Juan del Sur"', 'location' => null],

        // León
        ['department' => 'NI-LE', 'municipality' => 'León', 'code' => 'CT-LE-001', 'name' => 'Centro Tecnológico "Rigoberto López Pérez"', 'location' => null],
        ['department' => 'NI-LE', 'municipality' => 'La Paz Centro', 'code' => 'CT-LE-002', 'name' => 'Centro Tecnológico "La Paz Centro"', 'location' => null],
        ['department' => 'NI-LE', 'municipality' => 'El Sauce', 'code' => 'CT-LE-003', 'name' => 'Centro Tecnológico "El Sauce"', 'location' => null],

        // Chinandega
        ['department' => 'NI-CI', 'municipality' => 'Chinandega', 'code' => 'CT-CI-001', 'name' => 'Centro Tecnológico "Miguel Bonilla"', 'location' => null],
        ['department' => 'NI-CI', 'municipality' => 'El Viejo', 'code' => 'CT-CI-002', 'name' => 'Centro Tecnológico "El Viejo"', 'location' => null],
        ['department' => 'NI-CI', 'municipality' => 'Somotillo', 'code' => 'CT-CI-003', 'name' => 'Centro Tecnológico "Somotillo"', 'location' => null],

        // Estelí
        ['department' => 'NI-ES', 'municipality' => 'Estelí', 'code' => 'CT-ES-001', 'name' => 'Centro Tecnológico "Héroes y Mártires de Estelí"', 'location' => null],
        ['department' => 'NI-ES', 'municipality' => 'La Trinidad', 'code' => 'CT-ES-002', 'name' => 'Centro Tecnológico "La Trinidad"', 'location' => null],

        // Madriz
        ['department' => 'NI-MD', 'municipality' => 'Somoto', 'code' => 'CT-MD-001', 'name' => 'Centro Tecnológico "Somoto"', 'location' => null],
        ['department' => 'NI-MD', 'municipality' => 'Palacagüina', 'code' => 'CT-MD-002', 'name' => 'Centro Tecnológico "Palacagüina"', 'location' => null],

        // Nueva Segovia
        ['department' => 'NI-NS', 'municipality' => 'Ocotal', 'code' => 'CT-NS-001', 'name' => 'Centro Tecnológico "Ocotal"', 'location' => null],
        ['department' => 'NI-NS', 'municipality' => 'Jalapa', 'code' => 'CT-NS-002', 'name' => 'Centro Tecnológico "Jalapa"', 'location' => null],
        ['department' => 'NI-NS', 'municipality' => 'Quilalí', 'code' => 'CT-NS-003', 'name' => 'Centro Tecnológico "Quilalí"', 'location' => null],

        // Jinotega
        ['department' => 'NI-JI', 'municipality' => 'Jinotega', 'code' => 'CT-JI-001', 'name' => 'Centro Tecnológico "Jinotega"', 'location' => null],
        ['department' => 'NI-JI', 'municipality' => 'San Rafael del Norte', 'code' => 'CT-JI-002', 'name' => 'Centro Tecnológico "San Rafael del Norte"', 'location' => null],

        // Matagalpa
        ['department' => 'NI-MT', 'municipality' => 'Matagalpa', 'code' => 'CT-MT-001', 'name' => 'Centro Tecnológico "Matagalpa"', 'location' => null],
        ['department' => 'NI-MT', 'municipality' => 'Sébaco', 'code' => 'CT-MT-002', 'name' => 'Centro Tecnológico "Sébaco"', 'location' => null],
        ['department' => 'NI-MT', 'municipality' => 'Río Blanco', 'code' => 'CT-MT-003', 'name' => 'Centro Tecnológico "Río Blanco"', 'location' => null],

        // Boaco
        ['department' => 'NI-BO', 'municipality' => 'Boaco', 'code' => 'CT-BO-001', 'name' => 'Centro Tecnológico "Boaco"', 'location' => null],
        ['department' => 'NI-BO', 'municipality' => 'Camoapa', 'code' => 'CT-BO-002', 'name' => 'Centro Tecnológico "Camoapa"', 'location' => null],

        // Chontales
        ['department' => 'NI-CO', 'municipality' => 'Juigalpa', 'code' => 'CT-CO-001', 'name' => 'Centro Tecnológico "Germán Pomares Ordóñez"', 'location' => null],
        ['department' => 'NI-CO', 'municipality' => 'La Libertad', 'code' => 'CT-CO-002', 'name' => 'Centro Tecnológico "La Libertad"', 'location' => null],

        // Río San Juan
        ['department' => 'NI-SJ', 'municipality' => 'San Carlos', 'code' => 'CT-SJ-001', 'name' => 'Centro Tecnológico "San Carlos"', 'location' => null],
        ['department' => 'NI-SJ', 'municipality' => 'El Castillo', 'code' => 'CT-SJ-002', 'name' => 'Centro Tecnológico "El Castillo"', 'location' => null],

        // Costa Caribe Norte
        ['department' => 'NI-AN', 'municipality' => 'Puerto Cabezas', 'code' => 'CT-AN-001', 'name' => 'Centro Tecnológico "Bilwi"', 'location' => null],
        ['department' => 'NI-AN', 'municipality' => 'Siuna', 'code' => 'CT-AN-002', 'name' => 'Centro Tecnológico "Siuna"', 'location' => null],
        ['department' => 'NI-AN', 'municipality' => 'Bonanza', 'code' => 'CT-AN-003', 'name' => 'Centro Tecnológico "Bonanza"', 'location' => null],

        // Costa Caribe Sur
        ['department' => 'NI-AS', 'municipality' => 'Bluefields', 'code' => 'CT-AS-001', 'name' => 'Centro Tecnológico "Bluefields"', 'location' => null],
        ['department' => 'NI-AS', 'municipality' => 'Nueva Guinea', 'code' => 'CT-AS-002', 'name' => 'Centro Tecnológico "Nueva Guinea"', 'location' => null],
        ['department' => 'NI-AS', 'municipality' => 'Corn Island', 'code' => 'CT-AS-003', 'name' => 'Centro Tecnológico "Corn Island"', 'location' => null],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipalityIds = Municipality::query()
            ->with('department:id,code')
            ->get()
            ->mapWithKeys(fn (Municipality $municipality): array => [
                $municipality->department->code.'|'.$municipality->name => $municipality->id,
            ]);

        foreach (self::CENTERS as $center) {
            $key = $center['department'].'|'.$center['municipality'];
            $municipalityId = $municipalityIds[$key]
                ?? throw new RuntimeException("Unknown municipality [{$center['municipality']}] for department [{$center['department']}].");

            Center::updateOrCreate(
                ['code' => $center['code']],
                [
                    'municipality_id' => $municipalityId,
                    'name' => $center['name'],
                    'location' => $center['location'],
                ],
            );
        }
    }
}
