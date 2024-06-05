<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $products = Product::all();
    
            if ($products->isEmpty()) {
                $data = [
                    'message' => 'Productos inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Productos encontrados',
                'data' => $products,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los productos',
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function isActive()
{
    try {
        $products = Product::where('is_active', true)->with('brand')->get();

        if ($products->isEmpty()) {
            $data = [
                'message' => 'Productos inexistentes',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Productos encontrados',
            'data' => $products,
            'status' => Response::HTTP_OK,
        ];
        return response()->json($data, Response::HTTP_OK);

    } catch (\Exception $e) {
        $data = [
            'message' => 'Error al obtener los productos',
            'data' => null,
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ];
        return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    public function searchName(Request $request){
        try {
            $name = $request->query('name');

            if ($name) {
                $products = Product::where('name', 'LIKE', '%' . $name . '%')->get();
            } else {
                // Si no se proporciona el parámetro, retornar todos los productos
                $products = Product::all();
            }

            if ($products->isEmpty()) {
                $data = [
                    'message' => 'Productos inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Productos encontrados',
                'data' => $products,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);

        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los productos',
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
            'id' => [
                'required','string','max:255','unique:products,id'  
            ],
            'name' => [
                'required', 'string', 'max:255'
            ],
            'stock' => [
                'required', 'integer', 'min:0'
            ],
            'description' => [
                'nullable', 'string', 'max:1000'
            ],
            'price_sale' => [
                'required', 'numeric', 'min:0'
            ],
            'price_buy' => [
                'required', 'numeric', 'min:0'
            ],
            'unit' => [
                'required', 'numeric', 'min:0'
            ],
            'unit_quantity_id' => [
                'required', 'integer', 'exists:unit_quantities,id'
            ],
            'business_id' => [
                'required', 'integer', 'exists:busines,id'
            ],
            'brand_id' => [
                'required', 'integer', 'exists:brands,id'
            ],
            'clasification_id' => [
                'required', 'integer', 'exists:clasifications,id'
            ],
            'provider_id' => [
                'required', 'integer', 'exists:providers,id'
            ],
        ], [
            'id.required' => 'El codigo del producto es obligatorio.',
            'id.unique' => "El codigo del producto ya existe",
            'id.string' => 'El codigo debe ser una cadena de texto.',
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre del producto no debe exceder de 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'stock.required' => 'Es necesario especificar la cantidad de stock.',
            'stock.integer' => 'La cantidad de stock debe ser un número entero.',
            'stock.min' => 'No se pueden registrar negativos en el stock.',
            'description.required' => 'La descripción del producto es obligatoria.',
            'unit.required' => 'La unidad de medida es obligatorio.',
            'unit.numeric' => 'La unidad de medida debe ser numérico.',
            'unit.min' => 'No se pueden registrar negativos.',
            'price_sale.required' => 'El precio de venta es obligatorio.',
            'price_sale.numeric' => 'El precio de venta debe ser numérico.',
            'price_sale.min' => 'No se pueden registrar negativos.',
            'price_buy.required' => 'El precio de compra es obligatorio.',
            'price_buy.numeric' => 'El precio de venta debe ser numérico.',
            'price_buy.min' => 'No se pueden registrar negativos.',
            'unit_quantity_id.exists' => 'La unidad de cantidad seleccionada no es válida.',
            'business_id.exists' => 'El negocio seleccionado no es válido.',
            'brand_id.exists' => 'La marca seleccionada no es válida.',
            'clasification_id.exists' => 'La clasificación seleccionada no es válida.',
            'provider_id.exists' => 'El proveedor seleccionado no es válido.',
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
            $products = Product::create($validator->validated());
            if (!$products) {
                $data = [
                    'message' => 'Error al crear el producto',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Venta creada',
                'data' => $products,
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
        $products = Product::find($id);
        if(!$products){
            $data = [
                'message' => 'Producto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Producto encontrada',
            'data' => $products->load(['clasification']),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $products = Product::find($id);
        if(!$products){
            $data = [
                'message' => 'Producto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255'
            ],
            'stock' => [
                'required', 'integer', 'min:0'
            ],
            'description' => [
                'nullable', 'string', 'max:1000'
            ],
            'price_sale' => [
                'required', 'numeric', 'min:0'
            ],
            'price_buy' => [
                'required', 'numeric', 'min:0'
            ],
            'image' =>[
                'nullable',
            ],
            'unit' => [
                'required', 'numeric', 'min:0'
            ],
            'unit_quantity_id' => [
                'required', 'integer', 'exists:unit_quantities,id'
            ],
            'business_id' => [
                'required', 'integer', 'exists:busines,id'
            ],
            'brand_id' => [
                'required', 'integer', 'exists:brands,id'
            ],
            'clasification_id' => [
                'required', 'integer', 'exists:clasifications,id'
            ],
            'provider_id' => [
                'required', 'integer', 'exists:providers,id'
            ],
        ], [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre del producto no debe exceder de 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'stock.required' => 'Es necesario especificar la cantidad de stock.',
            'stock.integer' => 'La cantidad de stock debe ser un número entero.',
            'stock.min' => 'No se pueden registrar negativos en el stock.',
            'description.required' => 'La descripción del producto es obligatoria.',
            'unit.required' => 'La unidad de medida es obligatorio.',
            'unit.numeric' => 'La unidad de medida debe ser numérico.',
            'unit.min' => 'No se pueden registrar negativos.',
            'price_sale.required' => 'El precio de venta es obligatorio.',
            'price_sale.numeric' => 'El precio de venta debe ser numérico.',
            'price_sale.min' => 'No se pueden registrar negativos.',
            'price_buy.required' => 'El precio de compra es obligatorio.',
            'price_buy.numeric' => 'El precio de venta debe ser numérico.',
            'price_buy.min' => 'No se pueden registrar negativos.',
            'unit_quantity_id.exists' => 'La unidad de cantidad seleccionada no es válida.',
            'business_id.exists' => 'El negocio seleccionado no es válido.',
            'brand_id.exists' => 'La marca seleccionada no es válida.',
            'clasification_id.exists' => 'La clasificación seleccionada no es válida.',
            'provider_id.exists' => 'El proveedor seleccionado no es válido.',
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
        $products->update($validator->validated());
        $data = [
            'message' => 'Venta actualizada',
            'data' => $products,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $products = Product::find($id);
        if(!$products){
            $data = [
                'message' => 'Producto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $products -> delete();

        $data = [
            'message' => 'Producto eliminado',
            'data' => $products,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    public function updatePartial(Request $request, $id){
        $products = Product::find($id);
        if(!$products){
            $data = [
                'message' => 'Producto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string', 'max:255'
            ],
            'stock' => [
                'integer', 'min:0'
            ],
            'description' => [
                'string', 'max:1000'
            ],
            'price_sale' => [
                'numeric', 'min:0'
            ],
            'price_buy' => [
                'numeric', 'min:0'
            ],
            'image' =>[
                'nullable',
            ],
            'unit' => [
                'numeric', 'min:0'
            ],
            'unit_quantity_id' => [
                'integer', 'exists:unit_quantities,id'
            ],
            'business_id' => [
                'integer', 'exists:busines,id'
            ],
            'brand_id' => [
                'integer', 'exists:brands,id'
            ],
            'clasification_id' => [
                'integer', 'exists:clasifications,id'
            ],
            'provider_id' => [
                'integer', 'exists:providers,id'
            ],
        ], [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre del producto no debe exceder de 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'stock.integer' => 'La cantidad de stock debe ser un número entero.',
            'stock.min' => 'No se pueden registrar negativos en el stock.',
            'unit.numeric' => 'La unidad de medida debe ser numérico.',
            'unit.min' => 'No se pueden registrar negativos.',
            'price_sale.numeric' => 'El precio de venta debe ser numérico.',
            'price_sale.min' => 'No se pueden registrar negativos.',
            'price_buy.numeric' => 'El precio de venta debe ser numérico.',
            'price_buy.min' => 'No se pueden registrar negativos.',
            'unit_quantity_id.exists' => 'La unidad de cantidad seleccionada no es válida.',
            'business_id.exists' => 'El negocio seleccionado no es válido.',
            'brand_id.exists' => 'La marca seleccionada no es válida.',
            'clasification_id.exists' => 'La clasificación seleccionada no es válida.',
            'provider_id.exists' => 'El proveedor seleccionado no es válido.',
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
            $products -> name = $request -> name;
            $updatedFields['name'] = $request->name;
        }
        if($request -> has('stock')){
            $products -> stock = $request -> stock;
            $updatedFields['stock'] = $request->stock;
        }
        if($request -> has('description')){
            $products -> description = $request -> description;
            $updatedFields['description'] = $request->description;
        }
        if ($request->has('price_sale')) {
            $products->price_sale = $request->price_sale;
            $products->is_active = true; 
            $updatedFields['price_sale'] = $request->price_sale;
        }
        if($request -> has('price_buy')){
            $products -> price_buy = $request -> price_buy;
            $updatedFields['price_buy'] = $request->price_buy;
        }
        if($request -> has('image')){
            $products -> image = $request -> image;
            $updatedFields['image'] = $request->image;
        }
        if($request -> has('unit')){
            $products -> unit = $request -> unit;
            $updatedFields['unit'] = $request->unit;
        }
        if($request -> has('unit_quantity_id')){
            $products -> unit_quantity_id = $request -> unit_quantity_id;
            $updatedFields['unit_quantity_id'] = $request->unit_quantity_id;
        }
        if($request -> has('business_id')){
            $products -> business_id = $request -> business_id;
            $updatedFields['business_id'] = $request->business_id;
        }
        if($request -> has('brand_id')){
            $products -> brand_id = $request -> brand_id;
            $updatedFields['brand_id'] = $request->brand_id;
        }
        if($request -> has('clasification_id')){
            $products -> clasification_id = $request -> clasification_id;
            $updatedFields['clasification_id'] = $request->clasification_id;
        }
        if($request -> has('provider_id')){
            $products -> provider_id = $request -> provider_id;
            $updatedFields['provider_id'] = $request->provider_id;
        }
        
        $products -> save();

        $data = [
            'message' => 'Producto actualizado',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
