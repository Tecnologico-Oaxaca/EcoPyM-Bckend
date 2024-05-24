<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterEmployeeController extends Controller
{
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
            'days' => [
                'required', 'array'
            ],
            'days.*.day_id' => [
                'required', 'exists:days,id'
            ],
            'days.*.is_work_day' => [
                'required', 'boolean'
            ],
        
            // Reglas del segundo validator
            'name' => [
                'required', 'string', 'max:50'
            ],
            'last_name' => [
                'nullable', 'string', 'max:50'
            ],
            'phone' => [
                'nullable', 'digits:10', Rule::unique('users', 'phone')
            ],
            'email' => [
                'nullable', 'string', 'max:50', 'email', Rule::unique('users', 'email')
            ],
            'password' => [
                'required', 'string', 'min:8'
            ],
            'image' => [
                'nullable',
            ],
            'branch_id' => [
                'required', 'exists:branches,id' 
            ],
            'role_id' => [
                'required', 'exists:roles,id'
            ],
        ], [
            // Mensajes de error del primer validator
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un número.',
            'salary.max' => 'El salario no puede ser mayor de 9999.99.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
            'work_shift_id.required' => 'El ID del turno de trabajo es obligatorio.',
            'work_shift_id.exists' => 'El ID del turno de trabajo no es válido.',
            'days.required' => 'Los días son obligatorios.',
            'days.array' => 'Los días deben ser un array.',
            'days.*.day_id.required' => 'El ID del día es obligatorio.',
            'days.*.day_id.exists' => 'El ID del día no es válido.',
            'days.*.is_work_day.required' => 'El campo de día laboral es obligatorio.',
            'days.*.is_work_day.boolean' => 'El campo de día laboral debe ser verdadero o falso.',
        
            // Mensajes de error del segundo validator
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 50 caracteres.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'last_name.max' => 'El apellido no puede tener más de 50 caracteres.',
            'phone.digits' => 'El teléfono debe contener exactamente 10 dígitos.',
            'phone.unique' => 'El teléfono ya está registrado.',
            'email.string' => 'El correo debe ser una cadena de texto.',
            'email.max' => 'El correo no puede tener más de 50 caracteres.',
            'email.email' => 'El correo debe ser una dirección de correo electrónico válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'branch_id.required' => 'El ID de la sucursal es obligatorio.',
            'branch_id.exists' => 'El ID de la sucursal no es válido.',
            'role_id.required' => 'El ID del rol es obligatorio.',
            'role_id.exists' => 'El ID del rol no es válido.',
        ]);
        

        if ($validator->fails()) {
            $data = [
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
            return response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {

            $user = User::create($validator->validated());
            $role = Role::findById($request->input('role_id'));
            $user->assignRole($role->name);

            $existingContract = Contract::where('user_id', $user->id)->first();
            if ($existingContract) {
                DB::rollBack();
                $data = [
                    'message' => 'El usuario ya tiene un contrato existente.',
                    'status' => Response::HTTP_CONFLICT,
                ];
                return response()->json($data, Response::HTTP_CONFLICT);
            }

            $contract = new Contract($validator->validated());
            $contract->user_id = $user->id;
            $contract->save();

            $dayData = collect($request->days)->mapWithKeys(function ($day) {
                return [$day['day_id'] => ['is_work_day' => $day['is_work_day']]];
            })->toArray();
            $contract->days()->sync($dayData);

            DB::commit();
            $data = [
                'message' => 'Usuario y contrato creados exitosamente',
                'data' => ['user' => $user, 'contract' => $contract->load('days')],
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear el usuario y el contrato',
                'error' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
