<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $comtracts = Contract::all();
    
            if ($comtracts->isEmpty()) {
                $data = [
                    'message' => 'Contratos inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Contratos encontrados',
                'data' => $comtracts->load('days'),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los contratos',
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
            'salary' => [
                'required', 'numeric', 'max:9999.99'
            ],
            'end_date' => [
                'nullable', 'date', 'after_or_equal:start_date'
            ],
            'work_shift_id' => [
                'required', 'exists:work_shifts,id'
            ],
            'user_id' => [
                'required', 'exists:users,id'
            ],
            'days' => [
                'required', 'array'
            ],
            'days.*.day_id' => [
                'required', 'exists:days,id'
            ],
            'days.*.is_work_day' => [
                'required', 'boolean'
            ],
        ], [
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un número.',
            'salary.max' => 'El salario no puede ser mayor de 9999.99.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'work_shift_id.required' => 'El ID del turno de trabajo es obligatorio.',
            'work_shift_id.exists' => 'El ID del turno de trabajo no es válido.',
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'user_id.exists' => 'El ID del usuario no es válido.',
            'days.required' => 'Los días son obligatorios.',
            'days.array' => 'Los días deben ser un array.',
            'days.*.day_id.required' => 'El ID del día es obligatorio.',
            'days.*.day_id.exists' => 'El ID del día no es válido.',
            'days.*.is_work_day.required' => 'El campo de día laboral es obligatorio.',
            'days.*.is_work_day.boolean' => 'El campo de día laboral debe ser verdadero o falso.'

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
        $existingContract = Contract::where('user_id', $request->user_id)->first();
        if ($existingContract) {
            return response()->json([
                'message' => 'El usuario ya tiene un contrato existente.',
                'data' => null,
                'status' => Response::HTTP_CONFLICT,
            ], Response::HTTP_CONFLICT);
        }
        
        try {
            $contracts = Contract::create($validator->validated());
            if (!$contracts) {
                $data = [
                    'message' => 'Error al crear el Contrato',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $dayData = collect($request->days)->mapWithKeys(function ($day) {
                return [$day['day_id'] => ['is_work_day' => $day['is_work_day']]];
            })->toArray();
            $contracts->days()->sync($dayData);

            $data = [
                'message' => 'Contrato creado',
                'data' => $contracts->load('days'),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el contrato',
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
        $contracts = Contract::find($id);
        if(!$contracts){
            $data = [
                'message' => 'Contrato no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Contrato encontrado',
            'data' => $contracts->load('days'),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $contracts = Contract::find($id);
        if(!$contracts){
            $data = [
                'message' => 'Contrato no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'salary' => [
                'required','numeric', 'max:9999.99'
            ],
            'start_date' => [
                'required', 'date'
            ],
            'end_date' => [
                'nullable', 'date', 'after_or_equal:start_date'
            ],
            'work_shift_id' => [
                'required', 'exists:work_shifts,id'
            ],
            'days' => [
                'required', 'array'
            ],
            'days.*.day_id' => [
                'required', 'exists:days,id'
            ],
            'days.*.is_work_day' => [
                'required', 'boolean'
            ],
        ], [
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un número.',
            'salary.max' => 'El salario no puede ser mayor de 9999.99.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'work_shift_id.required' => 'El ID del turno de trabajo es obligatorio.',
            'work_shift_id.exists' => 'El ID del turno de trabajo no es válido.',
            'days.required' => 'Los días son obligatorios.',
            'days.array' => 'Los días deben ser un array.',
            'days.*.day_id.required' => 'El ID del día es obligatorio.',
            'days.*.day_id.exists' => 'El ID del día no es válido.',
            'days.*.is_work_day.required' => 'El campo de día laboral es obligatorio.',
            'days.*.is_work_day.boolean' => 'El campo de día laboral debe ser verdadero o falso.'

        ]);

        if($validator ->fails()){
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator -> errors(),
                'data' => null,
                'status' => Response::HTTP_BAD_REQUEST
            ];
            return response() -> json($data,Response::HTTP_BAD_REQUEST);
        }

        $contracts->update($validator->validated());
        
        try {
            if ($request->has('days')) {
                $dayData = collect($request->days)->mapWithKeys(function ($day) {
                    return [$day['day_id'] => ['is_work_day' => $day['is_work_day']]];
                })->toArray();
                $contracts->days()->sync($dayData);
            }
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al actualizar los dias laborados y descanso',
                'error' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
            return response() -> json($data,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = [
            'message' => 'Contrato actualizado',
            'data' => $contracts->load('days'),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $contacts = Contract::find($id);
        if(!$contacts){
            $data = [
                'message' => 'Contrato no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $contacts -> delete();

        $data = [
            'message' => 'Contrato eliminado',
            'data' => $contacts,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){

        $contracts = Contract::find($id);
        if(!$contracts){
            $data = [
                'message' => 'Contrato no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'salary' => [
                'numeric', 'max:9999.99'
            ],
            'start_date' => [
                'date'
            ],
            'end_date' => [
                'sometimes', 'date', 'after_or_equal:start_date'
            ],
            'work_shift_id' => [
                'exists:work_shifts,id'
            ],
            'days' => [
                'sometimes', 'array'
            ],
            'days.*.day_id' => [
                'exists:days,id'
            ],
            'days.*.is_work_day' => [
                'boolean'
            ],
        ], [
            'salary.numeric' => 'El salario debe ser un número.',
            'salary.max' => 'El salario no puede ser mayor de 9999.99.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'work_shift_id.exists' => 'El ID del turno de trabajo no es válido.',
            'days.*.day_id.exists' => 'El ID del día no es válido.',
            'days.*.is_work_day.boolean' => 'El campo de día laboral debe ser verdadero o falso.'
        ]);

        if($validator ->fails()){
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator -> errors(),
                'data' => null,
                'status' => Response::HTTP_BAD_REQUEST
            ];
            return response() -> json($data,Response::HTTP_BAD_REQUEST);
        }
        $startDate = $request->start_date ?? $contracts->start_date;
        if ($request->has('end_date') && strtotime($request->end_date) < strtotime($startDate)) {
            return response()->json([
                'message' => 'La fecha fin debe ser despues de la fecha inicio',
                'data' => null,
                'status' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        $updatedFields = [];

        if ($request->has('salary')) {
            $contracts->salary = $request->salary;
            $updatedFields['salary'] = $request->salary;
        }
        if ($request->has('start_date')) {
            $contracts->start_date = $request->start_date;
            $updatedFields['start_date'] = $request->start_date;
        }
        if ($request->has('end_date')) {
            $contracts->end_date = $request->end_date;
            $updatedFields['end_date'] = $request->end_date;
        }
        if ($request->has('work_shift_id')) {
            $contracts->work_shift_id = $request->work_shift_id;
            $updatedFields['work_shift_id'] = $request->work_shift_id;
        }
        if ($request->has('days')) {
            $dayData = collect($request->days)->mapWithKeys(function ($day) {
                return [$day['day_id'] => ['is_work_day' => $day['is_work_day']]];
            })->toArray();
            $contracts->days()->sync($dayData);
            $updatedFields['days'] = $dayData;
        }

        $contracts->save();

        $data = [
            'message' => 'Contrato actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
