<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class VerifyProductController extends Controller
{
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            $data = [
                'message' => 'Producto no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }

        if ($product->is_active) {
            $data = [
                'message' => 'Producto añadido',
                'data' => $product,
                'status' => Response::HTTP_OK
            ];
        } else {
            $data = [
                'message' => 'Producto no está activo',
                'data' => [
                    'price_sale' => $product->price_sale
                ],
                'status' => Response::HTTP_OK
            ];
        }

        return response()->json($data, Response::HTTP_OK);
    }
}
