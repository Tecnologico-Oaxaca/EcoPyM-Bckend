<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $suggestion = Suggestion::all();
    
            if ($suggestion->isEmpty()) {
                $data = [
                    'message' => 'Sugerencias inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Sugerencias encontradas',
                'data' => $suggestion,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las Sugerencias',
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'quantity' => [
                'required','integer','min:0'
            ],
            'amountAprox' => [
                'required', 'numeric','min:0'
            ],
            'product_id' => [
                'required', 'exists:products,id'
            ],

        ], [
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad no puede ser menor que cero.',
            'amountAprox.required' => 'El monto aproximado es obligatorio.',
            'amountAprox.numeric' => 'El monto aproximado debe ser un número.',
            'amountAprox.min' => 'El monto aproximado no puede ser menor que cero.',
            'product_id.required' => 'El ID del producto es obligatorio.',
            'product_id.exists' => 'El ID del producto proporcionado no existe en la base de datos.',
        ]);
    
        if ($validator->fails()) {
            $data =[
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
                'data' => null,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
            return response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $suggestion = Suggestion::create($validator->validated());
            if (!$suggestion) {
                $data = [
                    'message' => 'Error al crear la Sugerencia',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Sugerencia creada',
                'data' => $suggestion,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la sugerencia' ,
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id){
        $suggestion = Suggestion::find($id);
        if(!$suggestion){
            $data = [
                'message' => 'Sugerencia no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Sugerencia encontrada',
            'data' => $suggestion,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $suggestion = Suggestion::find($id);
        if(!$suggestion){
            $data = [
                'message' => 'Sugerencia no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $suggestion -> delete();

        $data = [
            'message' => 'Sugerencia eliminada',
            'data' => $suggestion,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
}
