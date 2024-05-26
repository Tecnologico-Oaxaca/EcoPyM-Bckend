<?php

namespace App\Http\Controllers;

use App\Models\Payment_Method;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $methods = Payment_Method::all();
    
            if ($methods->isEmpty()) {
                $data = [
                    'message' => 'Tipos de pago inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Tipos de pago encontrados',
                'data' => $methods,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los tipos de pago'. $e->getMessage(),
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
                'required','string','max:30',
                Rule::unique('payment_methods', 'type') 
            ],
        ], [
            'type.required' => 'El tipo de pago es obligatorio',
            'type.string' => 'El tipo de pago debe ser una cadena de texto.',
            'type.max' => 'El tipo de pago no puede ser mayor a 30 caracteres.',
            'type.unique' => 'El tipo de pago ya existe.', 
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
            $methods = Payment_Method::create($validator->validated());
            if (!$methods) {
                $data = [
                    'message' => 'Error al crear el tipo de pago',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Tipo de pago creado',
                'data' => $methods,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el tipo de pago',
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
        $methods = Payment_Method::find($id);

        if(!$methods){
            $data = [
                'message' => 'Tipo de pago no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Tipo de pago encontrado',
            'data' => $methods,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $methods = Payment_Method::find($id);
        if(!$methods){
            $data = [
                'message' => 'Tipo de pago no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'type' => [
                'required','string','max:30',
                Rule::unique('payment_methods', 'type')->ignore($methods->id)
            ],
        ], [
            'type.required' => 'El tipo de pago es obligatorio',
            'type.string' => 'El tipo de pago debe ser una cadena de texto.',
            'type.max' => 'El tipo de pago no puede ser mayor a 30 caracteres.',
            'type.unique' => 'El tipo de pago ya existe.', 
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
        $methods->update($validator->validated());
        $data = [
            'message' => 'Tipo de pago actualizado',
            'data' => $methods,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $methods = Payment_Method::find($id);
        if(!$methods){
            $data = [
                'message' => 'Tipo de pago no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $methods -> delete();

        $data = [
            'message' => 'Tipo de pago eliminado',
            'data' => $methods,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    public function updatePartial(Request $request, $id){
        $methods = Payment_Method::find($id);
        if(!$methods){
            $data = [
                'message' => 'Tipo de pago no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'type' => [
                'string','max:30',
                Rule::unique('payment_methods', 'type')->ignore($methods->id) 
            ],

        ], [
            'type.string' => 'El tipo de pago debe ser una cadena de texto.',
            'type.max' => 'El tipo de pago no puede ser mayor a 50 caracteres.',
            'typw.unique' => 'El tipo de pago ya existe.',
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
            $methods -> type = $request -> type;
            $updatedFields['type'] = $request->type;
        }
        $methods -> save();

        $data = [
            'message' => 'Tipo de pago actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
