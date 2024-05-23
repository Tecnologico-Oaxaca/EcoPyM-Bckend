<?php

namespace App\Http\Controllers;

use App\Models\cash_opening;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class CashOpeningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $cashOpenings = cash_opening::all();
    
            if ($cashOpenings->isEmpty()) {
                $data = [
                    'message' => 'Aperturas de caja inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Apertura de cajas encontradas',
                'data' => $cashOpenings,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las cajas de apertura',
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
                'required','numeric','min:0',
            ],
            'received_amount' => [
                'required','numeric','min:0',
            ],
            'observation' => [
                'nullable','string','max:100',
            ],
            'user_id' => [
                'required', 'exists:users,id'
            ],

        ], [
            'amount.required' => 'El monto es requerido',
            'amount.numeric' => 'El monto debe ser un número',
            'amount.min' => 'El monto debe ser mayor a 0',
            'received_amount.required' => 'El monto recibido es requerido',
            'received_amount.numeric' => 'El monto recibido debe ser un número',
            'received_amount.min' => 'El monto recibido debe ser mayor a 0',
            'observation.string' => 'La observación debe ser un texto',
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
            $cashOpenings = cash_opening::create($validator->validated());
            if (!$cashOpenings) {
                $data = [
                    'message' => 'Error al registar la apertura de caja',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Caja de apertura creada',
                'data' => $cashOpenings,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la caja de apertura',
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);;
        }
    }

    /**
     * Display the specified resource.
     */
}
