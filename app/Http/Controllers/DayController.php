<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class DayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $days = Day::all();
    
            if ($days->isEmpty()) {
                $data = [
                    'message' => 'Dias inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Dias encontrados',
                'data' => $days,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los dias',
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
            'name' => [
                'required','string','max:50',
                Rule::unique('days', 'name') 
            ],
        ], [
            'name.required' => 'El Dia es obligatorio',
            'name.string' => 'El dia debe ser una cadena de texto.',
            'name.max' => 'El dia no puede ser mayor a 30 caracteres.',
            'name.unique' => 'El dia ya existe.', 
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
            $days = Day::create($validator->validated());
            if (!$days) {
                $data = [
                    'message' => 'Error al crear el dia',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Dia creado',
                'data' => $days,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el dia',
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
        $days = Day::find($id);

        if(!$days){
            $data = [
                'message' => 'Dia no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Dia encontrado',
            'data' => $days,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $days = Day::find($id);
        if(!$days){
            $data = [
                'message' => 'Dia no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required','string','max:30',
                Rule::unique('days', 'name')->ignore($days->id)
            ],
        ], [
            'name.required' => 'El dia es obligatorio',
            'name.string' => 'El dia debe ser una cadena de texto.',
            'name.max' => 'El dia no puede ser mayor a 30 caracteres.',
            'name.unique' => 'El dia ya existe.', 
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
        $days->update($validator->validated());
        $data = [
            'message' => 'Dia actualizado',
            'data' => $days,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $days = Day::find($id);
        if(!$days){
            $data = [
                'message' => 'Dia no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $days -> delete();

        $data = [
            'message' => 'Dia eliminado',
            'data' => $days,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $days = Day::find($id);
        if(!$days){
            $data = [
                'message' => 'Dia no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string','max:30',Rule::unique('days', 'name')->ignore($days->id) 
            ],

        ], [
            'name.string' => 'El dia debe ser una cadena de texto.',
            'name.max' => 'El dia no puede ser mayor a 30 caracteres.',
            'name.unique' => 'El dia ya existe.',
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
        if($request -> has('name')){
            $days -> name = $request -> name;
            $updatedFields['name'] = $request->name;
        }
        $days -> save();

        $data = [
            'message' => 'Dia actualizado',
            'restaurants' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
