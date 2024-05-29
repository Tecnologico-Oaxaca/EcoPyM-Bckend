<?php

namespace App\Http\Controllers;

use App\Models\Trend;
use App\Models\Product; 
use Illuminate\Http\Request;

class TrendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trends = Trend::all();
        return response()->json($trends);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Trend $trend)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trend $trend)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trend $trend)
    {
        //
    }
     /**
     * Compara las tendencias con los productos y muestra aquellas que no están en productos.
     */
    public function compararConProductos(Request $request)
    {
        // Verificar si se proporcionó un category_id y asignarlo
        $categoryId = $request->query('category_id');
    
        // Obtener las tendencias que correspondan al category_id dado
        $trends = Trend::where('category_id', $categoryId)->get();
    
        return response()->json($trends);
    }
    
}
