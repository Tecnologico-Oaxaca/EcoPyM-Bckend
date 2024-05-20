<?php

namespace App\Http\Controllers;

use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class WorkShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $workshifts = WorkShift::all();
    
            if ($workshifts->isEmpty()) {
                $data = [
                    'message' => 'Turnos inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Turnos encontrados',
                'data' => $workshifts,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los turnos',
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
                Rule::unique('work_shifts', 'name') 
            ],
        ], [
            'name.required' => 'El turno es obligatorio',
            'name.string' => 'El turno debe ser una cadena de texto.',
            'name.max' => 'El turno no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El turno ya existe.', 
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
            $workshifts = WorkShift::create($validator->validated());
            if (!$workshifts) {
                $data = [
                    'message' => 'Error al crear el turno',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Area creada',
                'data' => $workshifts,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el turno',
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
        $workshifts = WorkShift::find($id);

        if(!$workshifts){
            $data = [
                'message' => 'Turno no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Turno encontrado',
            'data' => $workshifts,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $workshifts = WorkShift::find($id);
        if(!$workshifts){
            $data = [
                'message' => 'Turno no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required','string','max:30',
                Rule::unique('work_shifts', 'name')->ignore($workshifts->id)
            ],
        ], [
            'name.required' => 'El turno es obligatorio',
            'name.string' => 'El turno debe ser una cadena de texto.',
            'name.max' => 'El turno no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El turno ya existe.', 
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
        $workshifts->update($validator->validated());
        $data = [
            'message' => 'Turno actualizado',
            'data' => $workshifts,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $workshifts = WorkShift::find($id);
        if(!$workshifts){
            $data = [
                'message' => 'Tunro no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $workshifts -> delete();

        $data = [
            'message' => 'Turno eliminado',
            'data' => $workshifts,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $workshifts = WorkShift::find($id);
        if(!$workshifts){
            $data = [
                'message' => 'Turno no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string','max:30',Rule::unique('work_shifts', 'name')->ignore($workshifts->id) 
            ],

        ], [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
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

        if($request -> has('name')){
            $workshifts -> name = $request -> name;
        }
        $workshifts -> save();

        $data = [
            'message' => 'Turno actualizado',
            'restaurants' => $workshifts,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
