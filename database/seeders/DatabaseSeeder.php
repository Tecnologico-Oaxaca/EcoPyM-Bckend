<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkShift;
use App\Models\Day;
use App\Models\Area;
use App\Models\Role;



// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['name' => 'Gerente', 'guard_name' => 'Supervisor'],
            ['name' => 'Cajero', 'guard_name' => 'Cajero'],
            ['name' => 'Almacenista', 'guard_name' => 'Almacen'],
        ];

        foreach ($areas as $areaName) {
            $area = Area::create([
                'name' => $areaName,
            ]);

            // Asociar roles a esta área
            foreach ($roles as $roleData) {
                $existingRole = Role::where('name', $roleData['name'])->where('guard_name', $roleData['guard_name'])->first();

                if (!$existingRole) {
                    $role = Role::create($roleData);
                } else {
                    $role = $existingRole;
                }

                $area->roles()->attach($role);
            }
        }
    }
}
