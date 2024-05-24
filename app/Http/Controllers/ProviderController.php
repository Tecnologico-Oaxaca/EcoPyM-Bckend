<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $providers = Provider::all();
    
            if ($providers->isEmpty()) {
                $data = [
                    'message' => 'Proveedores inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Proveedores encontrados',
                'data' => $providers->load(['companies']),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los proveedores',
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
                Rule::unique('providers', 'name') 
            ],
            'image' => [
                'nullable',
            ],
            'phone' => [
                'required','digits:10','numeric',
                Rule::unique('providers', 'phone')
            ],
            'company_ids' => [
                'required', 'array'
            ],
            'company_ids.*' => [
                'exists:companies,id'
            ]
        ], [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser un texto',
            'name.max' => 'El nombre debe tener un máximo de 50 caracteres',
            'name.unique' => 'El nombre ya existe',
            'phone.required' => 'El teléfono es requerido',
            'phone.digits' => 'El teléfono debe tener 10 dígitos',
            'phone.numeric' => 'El teléfono debe ser numérico',
            'phone.unique' => 'El teléfono ya existe',
            'company_ids.required' => 'Las empresas son requeridas',
            'company_ids.array' => 'Las empresas deben ser un array',
            'company_ids.*.exists' => 'Una de las empresas no existen'
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
            $providers = Provider::create($validator->validated());
            if (!$providers) {
                $data = [
                    'message' => 'Error al crear el proveedor',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $providers->companies()->sync($request->company_ids);
            $data = [
                'message' => 'Proveedor creado',
                'data' => $providers->load(['companies']),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el proveedor',
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
        $providers = Provider::find($id);

        if(!$providers){
            $data = [
                'message' => 'Proveedor no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Proveedor encontrado',
            'data' => $providers->load(['companies']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $providers = Provider::find($id);
        if(!$providers){
            $data = [
                'message' => 'Proveedor no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required','string','max:50',
                Rule::unique('providers', 'name')->ignore($providers->id)
            ],
            'image' => [
                'nullable',
            ],
            'phone' => [
                'required','digits:10','numeric',
                Rule::unique('providers', 'phone')->ignore($providers->id)
            ],
            'company_ids' => [
                'required', 'array'
            ],
            'company_ids.*' => [
                'exists:companies,id'
            ]
        ], [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser un texto',
            'name.max' => 'El nombre debe tener un máximo de 50 caracteres',
            'name.unique' => 'El nombre ya existe',
            'phone.required' => 'El teléfono es requerido',
            'phone.digits' => 'El teléfono debe tener 10 dígitos',
            'phone.numeric' => 'El teléfono debe ser numérico',
            'phone.unique' => 'El teléfono ya existe',
            'company_ids.required' => 'Las empresas son requeridas',
            'company_ids.array' => 'Las empresas deben ser un array',
            'company_ids.*.exists' => 'Una de las empresas no existen',
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
        $providers->update($validator->validated());
        $providers->companies()->sync($request->company_ids);
        $data = [
            'message' => 'Proveedor actualizado',
            'data' => $providers->load(['companies']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $providers = Provider::find($id);
        if(!$providers){
            $data = [
                'message' => 'Proveedor no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $providers -> delete();

        $data = [
            'message' => 'Proveedor eliminado',
            'data' => $providers,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $providers = Provider::find($id);
        if(!$providers){
            $data = [
                'message' => 'Proveedor no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string','max:50',
                Rule::unique('providers', 'name')->ignore($providers->id)
            ],
            'image' => [
                'sometimes',
            ],
            'phone' => [
                'digits:10','numeric',
                Rule::unique('providers', 'phone')->ignore($providers->id)
            ],
            'company_ids' => [
                'sometimes', 'array','min:1',
                'exists:companies,id'
            ],
        ], [
            'name.string' => 'El nombre debe ser un texto',
            'name.max' => 'El nombre debe tener un máximo de 50 caracteres',
            'name.unique' => 'El nombre ya existe',
            'phone.digits' => 'El teléfono debe tener 10 dígitos',
            'phone.numeric' => 'El teléfono debe ser numérico',
            'phone.unique' => 'El teléfono ya existe',
            'company_ids.array' => 'La lista de empresas debe ser un array',
            'company_ids.min' => 'La lista de empresas debe tener al menos un elemento',
            'company_ids.exists' => 'Una de las empresas no existe',
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
            $providers -> name = $request -> name;
            $updatedFields['name'] = $request->name;
        }
        if($request -> has('image')){
            $providers -> image = $request -> image;
            $updatedFields['image'] = $request->image;
        }
        if($request -> has('phone')){
            $providers -> phone = $request -> phone;
            $updatedFields['phone'] = $request->phone;
        }
        if($request -> has('company_ids')){
            $providers->companies()->sync($request->company_ids);
            $updatedFields['company_ids'] = $request->company_ids;
        }
        $providers -> save();

        $data = [
            'message' => 'Proveedor actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
