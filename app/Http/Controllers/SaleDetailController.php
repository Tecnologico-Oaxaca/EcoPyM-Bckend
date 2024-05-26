<?php

namespace App\Http\Controllers;

use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class SaleDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $salDetail = SaleDetail::all();
    
            if ($salDetail->isEmpty()) {
                $data = [
                    'message' => 'Detalles de ventas inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Detalles de venta encontradas',
                'data' => $salDetail,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los detalles de ventas',
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
            'price_buy' => [
                'required', 'numeric', 'min:0'
            ],
            'price_sale' => [
                'required', 'numeric', 'min:0'
            ],
            'quantity' => [
                'required', 'integer', 'min:0'
            ],
            'discount' => [
                'required', 'numeric', 'min:0'
            ],
            'sale_id' => [
                'required', 'exists:sales,id'
            ],
            'product_id' => [
                'required', 'string','exists:products,id'
            ],
        ], [
            'price_buy.required' => 'El precio de compra es obligatorio.',
            'price_buy.numeric' => 'El precio de compra debe ser un valor numérico.',
            'price_buy.min' => 'El precio de compra no puede ser menor a cero.',
            'price_sale.required' => 'El precio de venta es obligatorio.',
            'price_sale.numeric' => 'El precio de venta debe ser un valor numérico.',
            'price_sale.min' => 'El precio de venta no puede ser menor a cero.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad no puede ser menor a cero.',
            'discount.required' => 'El descuento es obligatorio.',
            'discount.numeric' => 'El descuento debe ser un valor numérico.',
            'discount.min' => 'El descuento no puede ser menor a cero.',
            'sale_id.required' => 'El ID de la venta es obligatorio.',
            'sale_id.exists' => 'El ID de la venta proporcionado no existe.',
            'product_id.required' => 'El ID del producto es obligatorio.',
            'product_id.string' => 'El ID del producto debe ser una cadena de texto.',
            'product_id.exists' => 'El ID del producto proporcionado no existe.',
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
            $saleDetail = SaleDetail::create($validator->validated());
            if (!$saleDetail) {
                $data = [
                    'message' => 'Error al crear el detalle de venta',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Detalle de venta creado',
                'data' => $saleDetail,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el detalle de venta' . $e->getMessage() ,
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
        $saleDetail = SaleDetail::find($id);
        if(!$saleDetail){
            $data = [
                'message' => 'Detalle de venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Detalle de venta encontrada',
            'data' => $saleDetail,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $saleDetail = saleDetail::find($id);
        if(!$saleDetail){
            $data = [
                'message' => 'Detalle de venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'price_buy' => [
                'required', 'numeric', 'min:0'
            ],
            'price_sale' => [
                'required', 'numeric', 'min:0'
            ],
            'quantity' => [
                'required', 'integer', 'min:0'
            ],
            'discount' => [
                'required', 'numeric', 'min:0'
            ],
        ], [
            'price_buy.required' => 'El precio de compra es obligatorio.',
            'price_buy.numeric' => 'El precio de compra debe ser un valor numérico.',
            'price_buy.min' => 'El precio de compra no puede ser menor a cero.',
            'price_sale.required' => 'El precio de venta es obligatorio.',
            'price_sale.numeric' => 'El precio de venta debe ser un valor numérico.',
            'price_sale.min' => 'El precio de venta no puede ser menor a cero.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad no puede ser menor a cero.',
            'discount.required' => 'El descuento es obligatorio.',
            'discount.numeric' => 'El descuento debe ser un valor numérico.',
            'discount.min' => 'El descuento no puede ser menor a cero.',
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
        $saleDetail->update($validator->validated());
        $data = [
            'message' => 'Detalle de venta actualizada',
            'data' => $saleDetail,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $saleDetail = SaleDetail::find($id);
        if(!$saleDetail){
            $data = [
                'message' => 'Detalle de Venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $saleDetail -> delete();

        $data = [
            'message' => 'Detalle de Venta eliminada',
            'data' => $saleDetail,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    public function updatePartial(Request $request, $id){
        $saleDetail = SaleDetail::find($id);
        if(!$saleDetail){
            $data = [
                'message' => 'Detalle de Venta no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'price_buy' => [
                'numeric', 'min:0'
            ],
            'price_sale' => [
                'numeric', 'min:0'
            ],
            'quantity' => [
                'integer', 'min:0'
            ],
            'discount' => [
                'numeric', 'min:0'
            ],
        ], [
            'price_buy.numeric' => 'El precio de compra debe ser un valor numérico.',
            'price_buy.min' => 'El precio de compra no puede ser menor a cero.',
            'price_sale.numeric' => 'El precio de venta debe ser un valor numérico.',
            'price_sale.min' => 'El precio de venta no puede ser menor a cero.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad no puede ser menor a cero.',
            'discount.numeric' => 'El descuento debe ser un valor numérico.',
            'discount.min' => 'El descuento no puede ser menor a cero.',
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

        if($request -> has('price_buy')){
            $saleDetail -> price_buy = $request -> price_buy;
            $updatedFields['price_buy'] = $request->price_buy;
        }
        if($request -> has('price_sale')){
            $saleDetail -> price_sale = $request -> price_sale;
            $updatedFields['price_sale'] = $request->price_sale;
        }
        if($request -> has('quantity')){
            $saleDetail -> quantity = $request -> quantity;
            $updatedFields['quantity'] = $request->quantity;
        }
        if($request -> has('discount')){
            $saleDetail -> discount = $request -> discount;
            $updatedFields['discount'] = $request->discount;
        }

        $saleDetail -> save();

        $data = [
            'message' => 'Detalle de venta actualizada',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
