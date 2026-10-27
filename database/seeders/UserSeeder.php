<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Center;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@inatec.edu.ni'],
            [
                'name' => 'Administrador INATEC',
                'password' => 'password',
                'role' => User::ROLE_ADMIN,
            ],
        );

        $student = User::updateOrCreate(
            ['email' => 'estudiante@inatec.edu.ni'],
            [
                'name' => 'Estudiante INATEC',
                'password' => 'password',
                'role' => User::ROLE_STUDENT,
            ],
        );

        $center = Center::query()->where('code', 'CT-MN-001')->firstOrFail();

        $career = Career::query()
            ->whereBelongsTo($center)
            ->where('name', 'Técnico en Desarrollo de Software')
            ->firstOrFail();

        UserProfile::updateOrCreate(
            ['user_id' => $student->id],
            [
                'center_id' => $center->id,
                'career_id' => $career->id,
                'phone_whatsapp' => '50588888888',
                'bio' => 'Estudiante de desarrollo de software del Centro Tecnológico de Managua.',
            ],
        );
    }
}
