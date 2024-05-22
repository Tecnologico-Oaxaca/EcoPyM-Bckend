<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Mipyme;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $users = User::all();
    
            if ($users->isEmpty()) {
                $data = [
                    'message' => 'Usuarios inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Usuarios encontrados',
                'data' => $users->load(['roles', 'branch','assists']),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los usuarios',
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
            $data =[
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
                'data' => null,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
            return response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            DB::beginTransaction();
            $users = User::create($validator->validated());
            $role = Role::findById($request->input('role_id'));
            $users->assignRole($role->name);
            $mipyme = Mipyme::find($request->input('mipyme_id'));

        if ($mipyme) {
            $mipyme->update(['email' => $request->email]);
        }

        // Actualización del teléfono en Branch
        $branch = Branch::find($request->input('branch_id'));
        if ($branch) {
            $branch->update(['phone' => $request->phone]);
        }

        // Commit de la transacción
        DB::commit();
    
            if (!$users) {
                $data = [
                    'message' => 'Error al crear el usuario',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $role = Role::findById($request->input('role_id'));
            $users->assignRole($role->name);

            $data = [
                'message' => 'Usuario creado',
                'data' => $users->load(['roles','branch']),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear el usuario'. $e->getMessage(),
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
        $users = User::find($id);
        if(!$users){
            $data = [
                'message' => 'Usuario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Usuario encontrado',
            'data' => $users->load(['roles','branch']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $users = User::find($id);
        if(!$users){
            $data = [
                'message' => 'Usuario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:50'
            ],
            'last_name' => [
                'required', 'string', 'max:50'
            ],
            'phone' => [
                'required', 'digits:10', Rule::unique('users', 'phone')->ignore($users->id)
            ],
            'email' => [
                'required', 'string', 'max:50', 'email', Rule::unique('users', 'email')->ignore($users->id)
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
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 50 caracteres.',
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'last_name.max' => 'El apellido no puede tener más de 50 caracteres.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.digits' => 'El teléfono debe contener exactamente 10 dígitos.',
            'phone.unique' => 'El teléfono ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
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

        if($validator ->fails()){
            $data = [
                'message' => 'Error en la validación de los datos',
                'error' => $validator -> errors(),
                'data' => null,
                'status' => Response::HTTP_BAD_REQUEST
            ];
            return response() -> json($data,Response::HTTP_BAD_REQUEST);
        }
        $users->update($validator->validated());
        $role = Role::findById($request->input('role_id'));
        $users->syncRoles($role->name);
        $data = [
            'message' => 'Usuario actualizado',
            'data' => $users->load(['roles', 'branch']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    public function destroy($id){
        $users = User::find($id);
        if(!$users){
            $data = [
                'message' => 'Usuario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $users -> delete();

        $data = [
            'message' => 'Usuario eliminado',
            'data' => $users,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){

        $users = User::find($id);
        if(!$users){
            $data = [
                'message' => 'Usuario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string', 'max:50'
            ],
            'last_name' => [
                'string', 'max:50'
            ],
            'phone' => [
               'digits:10', Rule::unique('users', 'phone')->ignore($users->id)
            ],
            'email' => [
                'string', 'max:50', 'email', Rule::unique('users', 'email')->ignore($users->id)
            ],
            'password' => [
                'string', 'min:8'
            ],
            'image' => [
                'nullable',
            ],

            'branch_id' => [
                'sometimes', 'exists:branches,id' 
            ],
            'role_id' => [
                'sometimes', 'exists:roles,id'
            ],
        ], [
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
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'branch_id.exists' => 'El ID de la sucursal no es válido.',
            'role_id.exists' => 'El ID del rol no es válido.',
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

        if ($request->has('name')) {
            $users->name = $request->name;
            $updatedFields['name'] = $request->name;
        }
        if ($request->has('last_name')) {
            $users->last_name = $request->last_name;
            $updatedFields['last_name'] = $request->last_name;
        }
        if ($request->has('phone')) {
            $users->phone = $request->phone;
            $updatedFields['phone'] = $request->phone;
        }
        if ($request->has('email')) {
            $users->email = $request->email;
            $updatedFields['email'] = $request->email;
        }
        if($request -> has('password')){
            $users -> password = $request -> password;
        }
        if ($request->has('image')) {
            $users->image = $request->image;
            $updatedFields['image'] = $request->image;
        }
        if ($request->has('branch_id')) {
            $users->branch_id = $request->branch_id;
            $updatedFields['branch_id'] = $request->branch_id;
        }
        if ($request->has('role_id')) {
            $role = Role::findById($request->input('role_id'));
            $users->syncRoles($role->name);
            $updatedFields['role_id'] = $request->input('role_id');
        }

        $users->save();

        $data = [
            'message' => 'Uusario actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
