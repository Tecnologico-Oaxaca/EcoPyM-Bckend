<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $promotions = Promotion::all();
    
            if ($promotions->isEmpty()) {
                $data = [
                    'message' => 'Promociones inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Promociones encontradas',
                'data' => $promotions->load(['products']),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las promociones',
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
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'date_end' => [
                'required',
                'date',
                'after_or_equal:date_start',
            ],
            'description' => [
                'required',
                'string'
            ],
            'price_promotion' => [
                'required',
                'numeric',
                'min:0'
            ],
            'price_real' => [
                'required',
                'numeric',
                'min:0'
            ],
            'products' => [
                'required',
                'array',
            ],
            'products.*.id' => [
                'required',
                'exists:products,id',
            ],
            'products.*.stock' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'quantity.required' => 'La cantidad es requerida',
            'quantity.integer' => 'La cantidad debe ser un número entero',
            'quantity.min' => 'La cantidad debe ser al menos 1',
            'date_end.required' => 'La fecha final es requerida',
            'date_end.date' => 'El formato de la fecha final es incorrecto',
            'date_end.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha de inicio',
            'description.required' => 'La descripción es requerida',
            'description.string' => 'La descripción debe ser texto',
            'price_promotion.required' => 'El precio de promoción es requerido',
            'price_promotion.numeric' => 'El precio de promoción debe ser numérico',
            'price_promotion.min' => 'El precio de promoción debe ser positivo',
            'price_real.required' => 'El precio real es requerido',
            'price_real.numeric' => 'El precio real debe ser numérico',
            'price_real.min' => 'El precio real debe ser positivo',
            'products.required' => 'La lista de productos es requerida',
            'products.array' => 'La lista de productos debe ser un arreglo',
            'products.*.id.required' => 'El ID del producto es obligatorio',
            'products.*.id.exists' => 'El producto no existe',
            'products.*.stock.required' => 'El stock es requerido',
            'products.*.stock.integer' => 'El stock debe ser un número entero',
            'products.*.stock.min' => 'El stock debe ser al menos 1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
                'data' => null,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            DB::beginTransaction();
    
            $products = $request->input('products');
            $totalProductPriceBuy = 0;
    
            // Calcular la suma de product_price_buy de cada producto
            foreach ($products as $productData) {
                $product = Product::find($productData['id']);
    
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Producto no encontrado',
                        'errors' => ['product_id' => 'Producto con ID ' . $productData['id'] . ' no encontrado'],
                        'data' => null,
                        'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
    
                $totalProductPriceBuy += $product->price_buy * $productData['stock'];
            }
    
            // Validar que la suma de product_price_buy no sea mayor que price_promotion
            if ($totalProductPriceBuy > $request->input('price_promotion')) {
                DB::rollBack();
                return response()->json([
                    'message' => 'La promocion no genera ganacias',
                    'errors' => ['price_promotion' => 'La promocion no genera ganacias'],
                    'data' => null,
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            $promotionData = $request->only(['quantity', 'date_end', 'description', 'price_promotion', 'price_real']);
            $promotion = Promotion::create($promotionData);
    
            if (!$promotion) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Error al crear la promoción',
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
    
            // Asociar cada producto con la promoción en la tabla pivote usando el modelo ProductPromotion
            foreach ($products as $productData) {
                ProductPromotion::create([
                    'product_id' => $productData['id'],
                    'promotion_id' => $promotion->id,
                    'stock' => $productData['stock'],
                ]);
            }
    
            DB::commit();
            return response()->json([
                'message' => 'Promoción creada y asociada con los productos',
                'data' => $promotion,
                'status' => Response::HTTP_CREATED,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la promoción',
                'errors' => $e->getMessage(),
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id){
        $prmotions = Promotion::find($id);

        if(!$prmotions){
            $data = [
                'message' => 'Promoción no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Promoción encontrada',
            'data' => $prmotions->load(['products']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'date_end' => [
                'required',
                'date',
                'after_or_equal:date_start',
            ],
            'description' => [
                'required',
                'string'
            ],
            'price_promotion' => [
                'required',
                'numeric',
                'min:0'
            ],
            'price_real' => [
                'required',
                'numeric',
                'min:0'
            ],
            'products' => [
                'required',
                'array',
            ],
            'products.*.id' => [
                'required',
                'exists:products,id',
            ],
            'products.*.stock' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'quantity.required' => 'La cantidad es requerida',
            'quantity.integer' => 'La cantidad debe ser un número entero',
            'quantity.min' => 'La cantidad debe ser al menos 1',
            'date_end.required' => 'La fecha final es requerida',
            'date_end.date' => 'El formato de la fecha final es incorrecto',
            'date_end.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha de inicio',
            'description.required' => 'La descripción es requerida',
            'description.string' => 'La descripción debe ser texto',
            'price_promotion.required' => 'El precio de promoción es requerido',
            'price_promotion.numeric' => 'El precio de promoción debe ser numérico',
            'price_promotion.min' => 'El precio de promoción debe ser positivo',
            'price_real.required' => 'El precio real es requerido',
            'price_real.numeric' => 'El precio real debe ser numérico',
            'price_real.min' => 'El precio real debe ser positivo',
            'products.required' => 'La lista de productos es requerida',
            'products.array' => 'La lista de productos debe ser un arreglo',
            'products.*.id.required' => 'El ID del producto es obligatorio',
            'products.*.id.exists' => 'El producto no existe',
            'products.*.stock.required' => 'El stock es requerido',
            'products.*.stock.integer' => 'El stock debe ser un número entero',
            'products.*.stock.min' => 'El stock debe ser al menos 1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
                'data' => null,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        try {
            DB::beginTransaction();
    
            $promotion = Promotion::find($id);
    
            if (!$promotion) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Promoción no encontrada',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ], Response::HTTP_NOT_FOUND);
            }
    
            $products = $request->input('products');
            $totalProductPriceBuy = 0;
    
            foreach ($products as $productData) {
                $product = Product::find($productData['id']);
    
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Producto no encontrado',
                        'errors' => ['product_id' => 'Producto con ID ' . $productData['id'] . ' no encontrado'],
                        'data' => null,
                        'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
    
                $totalProductPriceBuy += $product->price_buy * $productData['stock'];
            }
    
            if ($totalProductPriceBuy > $request->input('price_promotion')) {
                DB::rollBack();
                return response()->json([
                    'message' => 'La promoción no genera ganancias',
                    'errors' => ['price_promotion' => 'La promoción no genera ganancias'],
                    'data' => null,
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            $promotion->update($request->only(['quantity', 'date_end', 'description', 'price_promotion', 'price_real']));
    
            $promotion->products()->detach();
    
            foreach ($products as $productData) {
                ProductPromotion::create([
                    'product_id' => $productData['id'],
                    'promotion_id' => $promotion->id,
                    'stock' => $productData['stock'],
                ]);
            }
    
            DB::commit();
            return response()->json([
                'message' => 'Promoción actualizada y productos asociados correctamente',
                'data' => $promotion,
                'status' => Response::HTTP_OK,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la promoción',
                'errors' => $e->getMessage(),
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $promotions = Promotion::find($id);
        if(!$promotions){
            $data = [
                'message' => 'Promoción no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $promotions -> delete();

        $data = [
            'message' => 'Promoción eliminada',
            'data' => $promotions,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id) {
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json([
                'message' => 'Promoción no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
    
        $validator = Validator::make($request->all(), [
            'quantity' => [
                'integer',
                'min:1',
            ],
            'date_end' => [
                'date',
                'after_or_equal:date_start',
            ],
            'description' => [
                'string'
            ],
            'price_promotion' => [
                'numeric',
                'min:0'
            ],
            'price_real' => [
                'numeric',
                'min:0'
            ],
            'products' => [
                'array',
            ],
            'products.*.id' => [
                'exists:products,id',
            ],
            'products.*.stock' => [
                'integer',
                'min:1',
            ],
        ], [
            'quantity.integer' => 'La cantidad debe ser un número entero',
            'quantity.min' => 'La cantidad debe ser al menos 1',
            'date_end.date' => 'El formato de la fecha final es incorrecto',
            'date_end.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha de inicio',
            'description.string' => 'La descripción debe ser texto',
            'price_promotion.numeric' => 'El precio de promoción debe ser numérico',
            'price_promotion.min' => 'El precio de promoción debe ser positivo',
            'price_real.numeric' => 'El precio real debe ser numérico',
            'price_real.min' => 'El precio real debe ser positivo',
            'products.array' => 'La lista de productos debe ser un arreglo',
            'products.*.id.exists' => 'El producto no existe',
            'products.*.stock.integer' => 'El stock debe ser un número entero',
            'products.*.stock.min' => 'El stock debe ser al menos 1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'data' => null,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        try {
            DB::beginTransaction();
    
            $totalProductPriceBuy = 0;
            $products = $request->input('products', []);
    
            foreach ($products as $productData) {
                $product = Product::find($productData['id']);
    
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Producto no encontrado',
                        'errors' => ['product_id' => 'Producto con ID ' . $productData['id'] . ' no encontrado'],
                        'data' => null,
                        'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
    
                $totalProductPriceBuy += $product->price_buy * $productData['stock'];
            }
    
            if ($request->has('price_promotion') && $totalProductPriceBuy > $request->input('price_promotion')) {
                DB::rollBack();
                return response()->json([
                    'message' => 'La promoción no genera ganancias',
                    'errors' => ['price_promotion' => 'La promoción no genera ganancias'],
                    'data' => null,
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            $updatedFields = [];
    
            if ($request->has('quantity')) {
                $promotion->quantity = $request->quantity;
                $updatedFields['quantity'] = $request->quantity;
            }
            if ($request->has('date_end')) {
                $promotion->date_end = $request->date_end;
                $updatedFields['date_end'] = $request->date_end;
            }
            if ($request->has('description')) {
                $promotion->description = $request->description;
                $updatedFields['description'] = $request->description;
            }
            if ($request->has('price_promotion')) {
                $promotion->price_promotion = $request->price_promotion;
                $updatedFields['price_promotion'] = $request->price_promotion;
            }
            if ($request->has('price_real')) {
                $promotion->price_real = $request->price_real;
                $updatedFields['price_real'] = $request->price_real;
            }
    
            $promotion->save();
    
            if (!empty($products)) {
                $promotion->products()->detach();
    
                foreach ($products as $productData) {
                    ProductPromotion::create([
                        'product_id' => $productData['id'],
                        'promotion_id' => $promotion->id,
                        'stock' => $productData['stock'],
                    ]);
                }
    
                $updatedFields['products'] = $products;
            }
    
            DB::commit();
    
            return response()->json([
                'message' => 'Promoción actualizada y productos asociados correctamente',
                'data' => $updatedFields,
                'status' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la promoción',
                'errors' => $e->getMessage(),
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}
