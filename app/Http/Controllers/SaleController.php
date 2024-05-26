<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $sales = Sale::all();
    
            if ($sales->isEmpty()) {
                $data = [
                    'message' => 'Ventas inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Ventas encontradas',
                'data' => $sales->load(['paymentMethods']),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las ventas'. $e->getMessage(),
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
            'total_sale' => [
                'required','numeric','min:0'
            ],
            'discount' => [
                'required', 'integer','min:0'
            ],
            'payment_method_ids' => [
                'required', 'array'
            ],
            'payment_method_ids.*' => [
                'exists:payment_methods,id'
            ],
            'user_id' => [
                'required', 'exists:users,id'
            ],

        ], [
            'total_sale.required'=> "El total de la venta es requerido",
            'total_sale.numeric' => "El total debe ser un número",
            'total_sale.min' => "El total no puede ser negativo",
            'discount.required' => "El descuento es requerido",
            'discount.integer' => "El descuento debe ser un número entero",
            'discount.min' => "El descuento no puede ser negativo",
            'payment_method_ids.required' => "La forma de pago es obligatoria",
            'payment_method_ids.*.exists' => "No existe la forma de pago",
            'user_id.required' => 'El usuario es requerido',
            'user_id.exists' => 'El usuario no existe',
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
            $sales = Sale::create($validator->validated());
            if (!$sales) {
                $data = [
                    'message' => 'Error al crear la venta',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $sales->paymentMethods()->sync($request->payment_method_ids);
            $data = [
                'message' => 'Venta creada',
                'data' => $sales->load(['paymentMethods']),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la venta' . $e->getMessage(),
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
        $sales = Sale::find($id);
        if(!$sales){
            $data = [
                'message' => 'Venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Venta encontrada',
            'data' => $sales->load(['paymentMethods']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $sales = Sale::find($id);
        if(!$sales){
            $data = [
                'message' => 'Venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'total_sale' => [
                'required','numeric','min:0'
            ],
            'discount' => [
                'required', 'integer','min:0'
            ],
            'payment_method_ids' => [
                'required', 'array'
            ],
            'payment_method_ids.*' => [
                'exists:payment_methods,id'
            ],

        ], [
            'total_sale.required'=> "El total de la venta es requerido",
            'total_sale.numeric' => "El total debe ser un número",
            'total_sale.min' => "El total no puede ser negativo",
            'discount.required' => "El descuento es requerido",
            'discount.integer' => "El descuento debe ser un número entero",
            'discount.min' => "El descuento no puede ser negativo",
            'payment_method_ids.required' => "La forma de pago es obligatoria",
            'payment_method_ids.*.exists' => "No existe la forma de pago",
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
        $sales->update($validator->validated());
        try {
            if ($request->has('payment_method_ids')) {
                $sales->paymentMethods()->sync($request->payment_method_ids);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la venta',
                'error' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = [
            'message' => 'Venta actualizada',
            'data' => $sales->load(['paymentMethods']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $sales = Sale::find($id);
        if(!$sales){
            $data = [
                'message' => 'Venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $sales -> delete();

        $data = [
            'message' => 'Venta eliminada',
            'data' => $sales,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $sales = Sale::find($id);
        if(!$sales){
            $data = [
                'message' => 'Venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'total_sale' => [
                'numeric','min:0'
            ],
            'discount' => [
                'integer','min:0'
            ],
            'payment_method_ids' => [
                'array'
            ],
            'payment_method_ids.*' => [
                'exists:payment_methods,id'
            ],

        ], [
            'total_sale.numeric' => "El total debe ser un número",
            'total_sale.min' => "El total no puede ser negativo",
            'discount.integer' => "El descuento debe ser un número entero",
            'discount.min' => "El descuento no puede ser negativo",
            'payment_method_ids.*.exists' => "No existe la forma de pago",
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

        if($request -> has('total_sale')){
            $sales -> total_sale = $request -> total_sale;
            $updatedFields['total_sale'] = $request->total_sale;
        }
        if($request -> has('discount')){
            $sales -> discount = $request -> discount;
            $updatedFields['discount'] = $request->discount;
        }
        if ($request->has('payment_method_ids')) {
            $sales->paymentMethods()->sync($request->payment_method_ids);
            $updatedFields['payment_method_ids']=$request->payment_method_ids;
        }
        $sales -> save();

        $data = [
            'message' => 'Venta actualizada',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
