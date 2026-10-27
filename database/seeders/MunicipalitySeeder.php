<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Municipality;
use Illuminate\Database\Seeder;
use RuntimeException;

class MunicipalitySeeder extends Seeder
{
    /**
     * Complete territorial division of Nicaragua, grouped by ISO department code.
     *
     * @var array<string, array<int, string>>
     */
    private const array MUNICIPALITIES = [
        'NI-MN' => [
            'Managua', 'Ciudad Sandino', 'El Crucero', 'Mateare', 'San Francisco Libre',
            'San Rafael del Sur', 'Ticuantepe', 'Tipitapa', 'Villa Carlos Fonseca',
        ],
        'NI-LE' => [
            'León', 'Achuapa', 'El Jicaral', 'El Sauce', 'La Paz Centro',
            'Larreynaga', 'Nagarote', 'Quezalguaque', 'Santa Rosa del Peñón', 'Telica',
        ],
        'NI-CI' => [
            'Chinandega', 'Chichigalpa', 'Cinco Pinos', 'Corinto', 'El Realejo', 'El Viejo',
            'Posoltega', 'Puerto Morazán', 'San Francisco del Norte', 'San Pedro del Norte',
            'Santo Tomás del Norte', 'Somotillo', 'Villanueva',
        ],
        'NI-GR' => ['Granada', 'Diriá', 'Diriomo', 'Nandaime'],
        'NI-MS' => [
            'Masaya', 'Catarina', 'La Concepción', 'Masatepe', 'Nandasmo',
            'Nindirí', 'Niquinohomo', 'San Juan de Oriente', 'Tisma',
        ],
        'NI-CA' => [
            'Jinotepe', 'Diriamba', 'Dolores', 'El Rosario', 'La Conquista',
            'La Paz de Carazo', 'San Marcos', 'Santa Teresa',
        ],
        'NI-RI' => [
            'Rivas', 'Altagracia', 'Belén', 'Buenos Aires', 'Cárdenas',
            'Moyogalpa', 'Potosí', 'San Jorge', 'San Juan del Sur', 'Tola',
        ],
        'NI-ES' => ['Estelí', 'Condega', 'La Trinidad', 'Pueblo Nuevo', 'San Juan de Limay', 'San Nicolás'],
        'NI-MD' => [
            'Somoto', 'Las Sabanas', 'Palacagüina', 'San José de Cusmapa', 'San Juan de Río Coco',
            'San Lucas', 'Telpaneca', 'Totogalpa', 'Yalagüina',
        ],
        'NI-NS' => [
            'Ocotal', 'Ciudad Antigua', 'Dipilto', 'El Jícaro', 'Jalapa', 'Macuelizo',
            'Mozonte', 'Murra', 'Quilalí', 'San Fernando', 'Santa María', 'Wiwilí de Nueva Segovia',
        ],
        'NI-JI' => [
            'Jinotega', 'El Cuá', 'La Concordia', 'San José de Bocay', 'San Rafael del Norte',
            'San Sebastián de Yalí', 'Santa María de Pantasma', 'Wiwilí de Jinotega',
        ],
        'NI-MT' => [
            'Matagalpa', 'Ciudad Darío', 'Esquipulas', 'Matiguás', 'Muy Muy', 'Rancho Grande',
            'Río Blanco', 'San Dionisio', 'San Isidro', 'San Ramón', 'Sébaco', 'Terrabona', 'Tuma-La Dalia',
        ],
        'NI-BO' => ['Boaco', 'Camoapa', 'San José de los Remates', 'San Lorenzo', 'Santa Lucía', 'Teustepe'],
        'NI-CO' => [
            'Juigalpa', 'Acoyapa', 'Comalapa', 'Cuapa', 'El Coral', 'La Libertad',
            'San Pedro de Lóvago', 'Santo Domingo', 'Santo Tomás', 'Villa Sandino',
        ],
        'NI-SJ' => ['San Carlos', 'El Almendro', 'El Castillo', 'Morrito', 'San Juan de Nicaragua', 'San Miguelito'],
        'NI-AN' => [
            'Puerto Cabezas', 'Bonanza', 'Mulukukú', 'Prinzapolka',
            'Rosita', 'Siuna', 'Waslala', 'Waspán',
        ],
        'NI-AS' => [
            'Bluefields', 'Corn Island', 'Desembocadura de Río Grande', 'El Ayote', 'El Rama', 'El Tortuguero',
            'Kukrahill', 'La Cruz de Río Grande', 'Muelle de los Bueyes', 'Nueva Guinea', 'Paiwas', 'Pearl Lagoon',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentIds = Department::query()->pluck('id', 'code');

        foreach (self::MUNICIPALITIES as $code => $municipalities) {
            $departmentId = $departmentIds[$code]
                ?? throw new RuntimeException("Unknown department code [{$code}].");

            foreach ($municipalities as $name) {
                Municipality::firstOrCreate([
                    'department_id' => $departmentId,
                    'name' => $name,
                ]);
            }
        }
    }
}
