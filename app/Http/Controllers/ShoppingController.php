<?php

namespace App\Http\Controllers;

use App\Models\Shopping;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ShoppingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $shoppin = Shopping::all();
    
            if ($shoppin->isEmpty()) {
                $data = [
                    'message' => 'Compras inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Compras encontradas',
                'data' => $shoppin,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las compras',
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
            'amount' => [
                'nullable', 'numeric','min:0'
            ],

        ], [
            'amount.numeric' => 'El monto aproximado debe ser un número.',
            'amount.min' => 'El monto aproximado no puede ser menor que cero.',
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
            $shoppin = Shopping::create($validator->validated());
            if (!$shoppin) {
                $data = [
                    'message' => 'Error al crear la compra',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Compra creada',
                'data' => $shoppin,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la compra' ,
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
        $shoppin = Shopping::find($id);
        if(!$shoppin){
            $data = [
                'message' => 'Compra no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Compra encontrada',
            'data' => $shoppin,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $shoppin = Shopping::find($id);
        if(!$shoppin){
            $data = [
                'message' => 'Compra no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'amount' => [
                'required', 'numeric','min:0'
            ],

        ], [
            'amount.required' => "El monto aproximado es requerido",
            'amount.numeric' => 'El monto aproximado debe ser un número.',
            'amount.min' => 'El monto aproximado no puede ser menor que cero.',
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
        $shoppin->update($validator->validated());
        $data = [
            'message' => 'Compra actualizada',
            'data' => $shoppin,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $shoppin = Shopping::find($id);
        if(!$shoppin){
            $data = [
                'message' => 'Compra no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $shoppin -> delete();

        $data = [
            'message' => 'Compra eliminada',
            'data' => $shoppin,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }


    public function updatePartial(Request $request, $id){
        $shoppin = Shopping::find($id);
        if(!$shoppin){
            $data = [
                'message' => 'Compra no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'amount' => [
                'sometimes', 'numeric','min:0'
            ],

        ], [
            'amount.numeric' => 'El monto aproximado debe ser un número.',
            'amount.min' => 'El monto aproximado no puede ser menor que cero.',
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

        if($request -> has('amount')){
            $shoppin -> amount = $request -> amount;
            $updatedFields['amount'] = $request->amount;
        }
        $shoppin -> save();

        $data = [
            'message' => 'Compra actualizada',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
