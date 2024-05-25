<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $roles = Role::all();
    
            if ($roles->isEmpty()) {
                $data = [
                    'message' => 'Roles inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Roles encontrados',
                'data' => $roles->load(['areas']),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los roles',
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
                'required','string','max:30',
                Rule::unique('roles', 'name') 
            ],
            'area_ids' => [
                'required', 'array'
            ],
            'area_ids.*' => [
                'exists:areas,id'
            ]
        ], [
            'name.required' => 'El rol es obligatorio',
            'name.string' => 'El rol debe ser una cadena de texto.',
            'name.max' => 'El rol no puede ser mayor a 30 caracteres.',
            'name.unique' => 'El rol ya existe.',
            'area_ids.required' => 'Debe proporcionar al menos un area',
            'area_ids.*.exists' => 'Uno de las areas proporcionados no existe'
         
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
            $roles = Role::create($validator->validated());
            if (!$roles) {
                $data = [
                    'message' => 'Error al crear el rol',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $roles->areas()->sync($request->area_ids);
            $data = [
                'message' => 'Rol creado',
                'data' => $roles->load(['areas']),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el rol',
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
        $roles = Role::find($id);

        if(!$roles){
            $data = [
                'message' => 'Rol no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Rol encontrad0',
            'data' => $roles->load(['areas']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $roles = Role::find($id);
        if(!$roles){
            $data = [
                'message' => 'Rol no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required','string','max:30',
                Rule::unique('roles', 'name')->ignore($roles->id)
            ],
            'area_ids' => [
                'required', 'array'
            ],
            'area_ids.*' => [
                'exists:areas,id'
            ]
        ], [
            'name.required' => 'El rol es obligatorio',
            'name.string' => 'El rol debe ser una cadena de texto.',
            'name.max' => 'El rol no puede ser mayor a 30 caracteres.',
            'name.unique' => 'El rol ya existe.',
            'area_ids.required' => 'Debe proporcionar al menos un area',
            'area_ids.*.exists' => 'Uno de las areas proporcionados no existe'
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
        $roles->update($validator->validated());
        try {
            if ($request->has('area_ids')) {
                $roles->areas()->sync($request->area_ids);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar los roles',
                'error' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = [
            'message' => 'Rol actualizado',
            'data' => $roles->load(['areas']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $roles = Role::find($id);
        if(!$roles){
            $data = [
                'message' => 'Rol no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $roles -> delete();

        $data = [
            'message' => 'Rol eliminado',
            'data' => $roles->load(['areas']),
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $roles = Role::find($id);
        if(!$roles){
            $data = [
                'message' => 'Rol no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string','max:30',Rule::unique('roles', 'name')->ignore($roles->id) 
            ],
            'area_ids' => [
                'sometimes', 'array','min:1',
                'exists:areas,id'
            ]

        ], [
            'name.string' => 'El rol debe ser una cadena de texto.',
            'name.max' => 'El rol no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El rol ya existe.',
            'area_ids.min' => 'El rol debe tener al menos un área.',
            'area_ids.exists' => 'Una o más áreas no existen.'
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
            $roles -> name = $request -> name;
            $updatedFields['name'] = $request->name;
        }
        $roles -> save();

        $data = [
            'message' => 'Rol actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
