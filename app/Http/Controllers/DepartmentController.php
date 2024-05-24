<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $departments = Department::all();
    
            if ($departments->isEmpty()) {
                $data = [
                    'message' => 'Departamentos inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Departamentos encontradas',
                'data' => $departments->load(['categories']),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los departamentos',
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
            'name' => [
                'required','string','max:50',
                Rule::unique('departments', 'name') 
            ],
            'category_ids' => [
                'required', 'array'
            ],
            'category_ids.*' => [
                'exists:categories,id'
            ]
        ], [
            'name.required' => 'El nombre del departamento es requerido',
            'name.string' => 'El nombre del departamento debe ser un texto',
            'name.max' => 'El nombre del departamento no puede tener más de 50 caracteres',
            'name.unique' => 'El nombre del departamento ya existe',
            'category_ids.required' => 'Seleccione por lo menos una categoría para el departamento',
            'category_ids.array' => 'La información enviada no es válida',
            'category_ids.exists' => 'Una o varias categorías seleccionadas no existen en la base de datos'
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
            $departments = Department::create($validator->validated());
            if (!$departments) {
                $data = [
                    'message' => 'Error al registar el departamento',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $departments->categories()->sync($request->category_ids);
            $data = [
                'message' => 'Departamento creado',
                'data' => $departments->load(['categories']),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el departamento',
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
        $departments = Department::find($id);
        if(!$departments){
            $data = [
                'message' => 'Departamento no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Departamento encontrado',
            'data' => $departments->load(['categories']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $departments = Department::find($id);
        if(!$departments){
            $data = [
                'message' => 'Departamento no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required','string','max:50',
                Rule::unique('departments', 'name')->ignore($departments->id)
            ],
            'category_ids' => [
                'required', 'array'
            ],
            'category_ids.*' => [
                'exists:categories,id'
            ]
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'category_ids.required' => 'Seleccione al menos una categoría.',
            'category_ids.array' => 'La información enviada no es válida.',
            'category_ids.*.exists' => 'Una o más categorías no existen.'
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
        $departments->update($validator->validated());
        $departments->categories()->sync($request->category_ids);
        $data = [
            'message' => 'Departamento actualizado',
            'data' => $departments->load(['categories']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $departments = Department::find($id);
        if(!$departments){
            $data = [
                'message' => 'Departamento no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $departments -> delete();

        $data = [
            'message' => 'Departamento eliminado',
            'data' => $departments,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    public function updatePartial(Request $request, $id){
        $departments = Department::find($id);
        if(!$departments){
            $data = [
                'message' => 'Departamento no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string','max:50',
                Rule::unique('departments', 'name')->ignore($departments->id) 
            ],
            'category_ids' => [
                'sometimes', 'array','min:1',
                'exists:categories,id'
            ],
        ], [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'category_ids.array' => 'La lista de empresas debe ser un array',
            'category_ids.min' => 'La lista de empresas debe tener al menos un elemento',
            'category_ids.exists' => 'Una de las empresas no existe',
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
            $departments -> name = $request -> name;
            $updatedFields['name'] = $request->name;
        }
        if($request -> has('category_ids')){
            $departments->categories()->sync($request->category_ids);
            $updatedFields['category_ids'] = $request->category_ids;
        }
        $departments -> save();

        $data = [
            'message' => 'Departamento actualizad0',
            'restaurants' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
