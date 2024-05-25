<?php

namespace App\Http\Controllers;

use App\Models\Mipyme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Validator;

class MipymeController extends Controller{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        
        try {
            $mipymes = Mipyme::all();
            if ($mipymes->isEmpty()) {
                $data = [
                    'message' => 'MIPyMEs inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'MIPyMEs encontrados',
                'data' => $mipymes->load(['businesses','branches.users']),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las MIPyMEs'. $e->getMessage(),
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
                'required','string','max:50',Rule::unique('mipymes', 'name') 
            ],
            'email' => [
                'nullable','string','max:50','email',Rule::unique('mipymes', 'email') 
            ],
            'image' => [
                'nullable',
            ],
            'business_ids' => [
                'required', 'array'
            ],
            'business_ids.*' => [
                'exists:busines,id'
            ]

        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'El email no es válido.',
            'email.max' => 'El email no puede ser mayor a 50 caracteres.',
            'email.unique' => 'El email ya existe.',
            'business_ids.required' => 'Debe proporcionar al menos un giro comercial',
            'business_ids.*.exists' => 'Uno de los giros comerciales proporcionados no existe'
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
            $mipymes = Mipyme::create($validator->validated());
            if (!$mipymes) {
                $data = [
                    'message' => 'Error al crear la MIPyME',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $mipymes->businesses()->sync($request->business_ids);
            $data = [
                'message' => 'MIPyME creada',
                'data' => $mipymes->load(['businesses','branches']),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la MIPyME',
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
        $mipymes = Mipyme::find($id);
        if(!$mipymes){
            $data = [
                'message' => 'MIPyME no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'MIPyME encontrada',
            'data' => $mipymes->load(['businesses','branches.users']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, $id){
        $mipymes = Mipyme::find($id);
        if(!$mipymes){
            $data = [
                'message' => 'MIPyME no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required','string','max:50',Rule::unique('mipymes', 'name')->ignore($mipymes->id)
 
            ],
            'email' => [
                'required','string','max:50','email',Rule::unique('mipymes', 'email')->ignore($mipymes->id) 
            ],
            'image' => [
                'nullable',
            ],
            'business_ids' => [
                'required', 'array'
            ],
            'business_ids.*' => [
                'exists:busines,id'
            ]

        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'email.required' => 'El email es obligatorio',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'El email no es válido.',
            'email.max' => 'El email no puede ser mayor a 50 caracteres.',
            'email.unique' => 'El email ya existe.',
            'business_ids.required' => 'Debe proporcionar al menos un giro comercial',
            'business_ids.*.exists' => 'Uno de los giros comerciales proporcionados no existe'
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
        $mipymes->update($validator->validated());
        try {
            if ($request->has('business_ids')) {
                $mipymes->businesses()->sync($request->business_ids);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar los giros comerciales',
                'error' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = [
            'message' => 'MIPyME actualizada',
            'data' => $mipymes->load(['businesses','branches']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

     /**
     * Remove the specified resource from storage.
     */

     public function destroy($id){
        $mipymes = Mipyme::find($id);
        if(!$mipymes){
            $data = [
                'message' => 'MIPyME no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $mipymes -> delete();

        $data = [
            'message' => 'MIPyME eliminada',
            'data' => $mipymes->load(['businesses','branches']),
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $mipymes = Mipyme::find($id);
        if(!$mipymes){
            $data = [
                'message' => 'MIPyME no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string','max:50',Rule::unique('mipymes', 'name')->ignore($mipymes->id) 
            ],
            'email' => [
                'string','max:50','email',Rule::unique('mipymes', 'email')->ignore($mipymes->id)
            ],
            'image' => [
                'nullable',
            ],
            'business_ids' => [
                'sometimes', 'array','min:1',
                'exists:busines,id'
            ],

        ], [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'El email no es válido.',
            'email.max' => 'El email no puede ser mayor a 50 caracteres.',
            'email.unique' => 'El email ya existe.',
            'business_ids' => 'La MIPyME debe tener al menos un giro comercial.',
            'area_ids.exists' => 'Uno o más giros comerciales no existen.'
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
            $mipymes -> name = $request -> name;
            $updatedFields['name'] = $request->name;
        }
        if($request -> has('email')){
            $mipymes -> email = $request -> email;
            $updatedFields['email'] = $request->email;
        }
        if($request -> has('image')){
            $mipymes -> image = $request -> image;
            $updatedFields['image'] = $request->image;

        }
        if ($request->has('business_ids')) {
            $mipymes->businesses()->sync($request->business_ids);
            $updatedFields['business_id']=$request->business_id;
        }
        $mipymes -> save();

        $data = [
            'message' => 'MIPyME actualizada',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
