<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $expense = ExpenseType::all();
    
            if ($expense->isEmpty()) {
                $data = [
                    'message' => 'Tipo de gastos inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Tipo de gastos encontrados',
                'data' => $expense,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los tipo de gastos',
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => [
                'required','string','max:30',
                Rule::unique('expense_types', 'type') 
            ],
        ], [
            'type.required' => 'El tipo de gasto es obligatorio',
            'type.string' => 'El tipo de gasto debe ser una cadena de texto.',
            'type.max' => 'El tipo de gasto no puede ser mayor a 30 caracteres.',
            'type.unique' => 'El tipo de gasto ya existe.', 
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
            $expense = ExpenseType::create($validator->validated());
            if (!$expense) {
                $data = [
                    'message' => 'Error al crear el tipo de gasto',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Tipo de gasto creado',
                'data' => $expense,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el tipo de gasto',
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
        $expense = ExpenseType::find($id);

        if(!$expense){
            $data = [
                'message' => 'Tipo de gasto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Tipo de gasto encontrado',
            'data' => $expense,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $expense = ExpenseType::find($id);
        if(!$expense){
            $data = [
                'message' => 'Tipo de gasto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'type' => [
                'required','string','max:30',
                Rule::unique('expense_types', 'type')->ignore($expense->id)
            ],
        ], [
            'type.required' => 'El tipo de gasto es obligatorio',
            'type.string' => 'El tipo de gasto debe ser una cadena de texto.',
            'type.max' => 'El tipo de gasto no puede ser mayor a 30 caracteres.',
            'type.unique' => 'El tipo de gasto ya existe.', 
        ]);

        if($validator ->fails()){
            $data = [
                'message' => 'Error en la validación de los datos',
                'error' => $validator -> errors(),
                'data' => null,
                'status' => Response::HTTP_BAD_REQUEST
            ];
            return response() -> json($data,Response::HTTP_BAD_REQUEST);
        }
        $expense->update($validator->validated());
        $data = [
            'message' => 'Tipo de gasto actualizado',
            'data' => $expense,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $expense = ExpenseType::find($id);
        if(!$expense){
            $data = [
                'message' => 'Tipo de gasto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $expense -> delete();

        $data = [
            'message' => 'Tipo de gasto eliminado',
            'data' => $expense,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    public function updatePartial(Request $request, $id){
        $expense = ExpenseType::find($id);
        if(!$expense){
            $data = [
                'message' => 'Tipo de gasto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'type' => [
                'string','max:30',
                Rule::unique('expense_types', 'type')->ignore($expense->id) 
            ],

        ], [
            'type.string' => 'El tipo de gasto debe ser una cadena de texto.',
            'type.max' => 'El tipo de gasto no puede ser mayor a 30 caracteres.',
            'type.unique' => 'El tipo de gasto ya existe.',
        ]);

        if($validator ->fails()){
            $data = [
                'message' => 'Error en la validación de los datos',
                'error' => $validator -> errors(),
                'data' => null,
                'status' => Response::HTTP_BAD_REQUEST
            ];
            return response() -> json($data,Response::HTTP_BAD_REQUEST);
        }
        
        $updatedFields = [];

        if($request -> has('type')){
            $expense -> type = $request -> type;
            $updatedFields['type'] = $request->type;
        }
        $expense -> save();

        $data = [
            'message' => 'Tipo de gasto actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
