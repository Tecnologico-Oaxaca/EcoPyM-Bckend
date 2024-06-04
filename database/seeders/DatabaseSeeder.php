<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkShift;
use App\Models\Day;
use App\Models\Area;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $turnos = [
            'Turno completo mañana',
            'Turno completo tarde',
            'Medio turno tarde',
            'Medio turno mañana'
        ];

        foreach ($turnos as $turno) {
            WorkShift::create([
                'name' => $turno,
            ]);
        }

        $days = [
            'Lunes',
            'Martes',
            'Miércoles',
            'Jueves',
            'Viernes',
            'Sábado',
            'Domingo'
        ];

        foreach ($days as $day) {
            Day::create([
                'name' => $day,
            ]);
        }

        $areas = [
            'Almacén',
            'Ventas',
            'Proveedores',
            'Registro',
        ];

        $roles = [
            ['name' => 'Gerente', 'guard_name' => 'web'],
            ['name' => 'Cajero', 'guard_name' => 'web'],
            ['name' => 'Almacenista', 'guard_name' => 'web'],
        ];

        foreach ($areas as $areaName) {
            $area = Area::create(['name' => $areaName]);

            foreach ($roles as $roleData) {
                $role = Role::firstOrCreate([
                    'name' => $roleData['name'],
                    'guard_name' => $roleData['guard_name']
                ]);

                // Verifica si el área ya tiene asignado el rol para evitar duplicaciones
                if (!$area->roles->contains($role->id)) {
                    $area->roles()->attach($role);
                }
            }
        }
    }
}
