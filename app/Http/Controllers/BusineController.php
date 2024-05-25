<?php

namespace App\Http\Controllers;

use App\Models\Busine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class BusineController extends Controller{
    /**
     * Display a listing of the resource.
     */

    public function index() {
        try {
            $businesses = Busine::all();
    
            if ($businesses->isEmpty()) {
                $data = [
                    'message' => 'Giros comerciales inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Giros comerciales encontrados',
                'data' => $businesses,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los giros comerciales',
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
                'required',
                'string',
                'max:50',
                Rule::unique('busines', 'type') 
            ],
        ], [
            'type.required' => 'El giro comercial es obligatorio',
            'type.string' => 'El giro comercial debe ser una cadena de texto.',
            'type.max' => 'El giro comercial no puede ser mayor a 50 caracteres.',
            'type.unique' => 'El giro comercial ya existe.', 
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
            $business = Busine::create(['type' => $request->type]);
            if (!$business) {
                $data = [
                    'message' => 'Error al crear el giro comercial',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Giro comercial creado',
                'data' => $business,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el giro comercial',
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);;
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $business = Busine::find($id);

        if(!$business){
            $data = [
                'message' => 'Giro empresarial no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Giro empresarial encontrado',
            'data' => $business,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $business = Busine::find($id);
        if(!$business){
            $data = [
                'message' => 'Giro comercial no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'type' => [
                'required',
                'string',
                'max:50',
                Rule::unique('busines', 'type') 
            ],
        ], [
            'type.required' => 'El giro comercial es obligatorio',
            'type.string' => 'El giro comercial debe ser una cadena de texto.',
            'type.max' => 'El giro comercial no puede ser mayor a 50 caracteres.',
            'type.unique' => 'El giro comercial ya existe.',
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
        $business -> type = $request -> type;
        $business -> save();

        $data = [
            'message' => 'Giro comercial actualizado',
            'data' => $business,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $business = Busine::find($id);
        if(!$business){
            $data = [
                'message' => 'Giro comercial no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $business -> delete();

        $data = [
            'message' => 'Giro comercial eliminado',
            'data' => $business,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){

        $business = Busine::find($id);
        if(!$business){
            $data = [
                'message' => 'Giro comercial no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'type' => [
                'required',
                'string',
                'max:50',
                Rule::unique('busines', 'type') 
            ],[
                'type.required' => 'El tipo es requerido',
                'type.string' => 'El tipo debe ser un string',
                'type.max' => 'El tipo no puede ser mayor a 50 caracteres',
                'type.unique' => 'El tipo ya existe'
            ]
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
            $business -> type = $request -> type;
            $updatedFields['type'] = $request->type;
        }
        $business -> save();

        $data = [
            'message' => 'Giro comercial actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
