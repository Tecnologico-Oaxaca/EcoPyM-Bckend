<?php

namespace App\Console\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class RellenarBusines extends Command
{
    protected $signature = 'app:rellenar-busines';
    protected $description = 'Transfiere los giros desde product_globals a EcoPyM';

    public function handle()
    {
        $this->info('Conectando a la base de datos product_globals...');

        // Obtener los registros de la tabla giros excluyendo aquellos con name NULL
        $giros = DB::connection('product_globals')
            ->table('giros')
            ->whereNotNull('name')
            ->get();

        $this->info('Conectando a la base de datos EcoPyM...');

        // Insertar los registros en la tabla busines de la base de datos EcoPyM
        foreach ($giros as $giro) {
            // Verificar si el registro ya existe
            $exists = DB::table('busines')
                ->where('type', $giro->name)
                ->exists();

            if (!$exists) {
                DB::table('busines')->insert([
                    'type' => $giro->name,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(), // Si deseas también llenar updated_at con la misma fecha y hora
                ]);
                $this->info("Registro insertado: {$giro->name}");
            } else {
                $this->info("Registro ya existe: {$giro->name}");
            }
        }

        $this->info('Datos transferidos exitosamente.');
    }
}
