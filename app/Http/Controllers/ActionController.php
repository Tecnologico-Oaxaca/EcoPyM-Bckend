<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $actions = Action::all();
    
            if ($actions->isEmpty()) {
                $data = [
                    'message' => 'Acciones inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Acciones encontradas',
                'data' => $actions,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las Acciones',
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
                Rule::unique('actions', 'type') 
            ],
        ], [
            'type.required' => 'La acción es obligatorio',
            'type.string' => 'La acción debe ser una cadena de texto.',
            'type.max' => 'La acción no puede ser mayor a 30 caracteres.',
            'type.unique' => 'La acción ya existe.', 
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
            $actions = Action::create($validator->validated());
            if (!$actions) {
                $data = [
                    'message' => 'Error al crear la acción',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Acción creada',
                'data' => $actions,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la acción',
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
        $actions = Action::find($id);

        if(!$actions){
            $data = [
                'message' => 'Acción no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Accción encontrada',
            'data' => $actions,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $actions = Action::find($id);
        if(!$actions){
            $data = [
                'message' => 'Accion no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'type' => [
                'required','string','max:30',
                Rule::unique('actions', 'type') 
            ],
        ], [
            'type.required'=> "La acción es requerida",
            'type.string' => 'La acción debe ser una cadena de texto.',
            'type.max' => 'La acción no puede ser mayor a 30 caracteres.',
            'type.unique' => 'La acción ya existe.', 
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
        $actions->update($validator->validated());
        $data = [
            'message' => 'Acción actualizada',
            'data' => $actions,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $actions = Action::find($id);
        if(!$actions){
            $data = [
                'message' => 'Acción no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $actions -> delete();

        $data = [
            'message' => 'Accion eliminada',
            'data' => $actions,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $actions = Action::find($id);
        if(!$actions){
            $data = [
                'message' => 'Acción no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'type' => [
                'string','max:30',
                Rule::unique('actions', 'type') 
            ],
        ], [
            'type.string' => 'La acción debe ser una cadena de texto.',
            'type.max' => 'La acción no puede ser mayor a 30 caracteres.',
            'type.unique' => 'La acción ya existe.', 
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
            $actions -> type = $request -> type;
            $updatedFields['type'] = $request->type;
        }
        $actions -> save();

        $data = [
            'message' => 'Acción actualizada',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
