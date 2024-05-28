<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransferirDatosService
{
    public function transferirDatos(array $giroIds)
    {
        foreach ($giroIds as $giroId) {
            info("Buscando giro con ID: $giroId en product_globals...");

            try {
                $giro = DB::connection('product_globals')->table('giros')
                           ->where('id', $giroId)->first();

                if (!$giro) {
                    info("Giro no encontrado: {$giroId}");
                    continue;
                }

                info("Giro encontrado: ID = {$giro->id}, Nombre = {$giro->name}");

                // Ejecutar lógica para clasificaciones y marcas
                $this->rellenarClasificacion($giro->id);
                $this->rellenarBrand($giro->id);

            } catch (\Exception $e) {
                info("Error al conectar o consultar la base de datos: " . $e->getMessage());
            }
        }
    }

    protected function rellenarClasificacion($giroId)
    {
        info("Rellenando clasificaciones para el giro ID: $giroId");

        $clasificaciones = DB::connection('product_globals')
            ->table('global_products')
            ->where('giro_id', $giroId)
            ->join('categories', 'global_products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name')
            ->distinct()
            ->get();

        if ($clasificaciones->isEmpty()) {
            info("No se encontraron clasificaciones para el giro: $giroId");
            return;
        }

        foreach ($clasificaciones as $clasificacion) {
            info("Clasificación: {$clasificacion->category_name}");

            $existingClasificacion = DB::table('clasifications')
                ->where('name', $clasificacion->category_name)
                ->first();

                if (!$existingClasificacion) {
                    DB::table('clasifications')->insert([
                        'name' => $clasificacion->category_name,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    info("Clasificación insertada: {$clasificacion->category_name}");
                } else {
                    info("Clasificación ya existente: {$clasificacion->category_name}");
                }
        }
    }

    protected function rellenarBrand($giroId)
    {
        info("Rellenando marcas para el giro ID: $giroId");

        $brands = DB::connection('product_globals')
            ->table('global_products')
            ->where('giro_id', $giroId)
            ->select('brand')
            ->distinct()
            ->get();

        if ($brands->isEmpty()) {
            info("No se encontraron marcas para el giro: $giroId");
            return;
        }

        foreach ($brands as $brand) {
            info("Marca: {$brand->brand}");

            $existingBrand = DB::table('brands')
                ->where('name', $brand->brand)
                ->first();

            if (!$existingBrand) {
                DB::table('brands')->insert([
                    'name' => $brand->brand,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                info("Marca insertada: {$brand->brand}");
            } else {
                info("Marca ya existente: {$brand->brand}");
            }
        }
    }
}
