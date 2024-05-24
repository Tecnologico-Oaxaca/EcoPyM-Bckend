<?php

namespace App\Http\Controllers;

use App\Models\UnitQuantity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UnitQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        
        try {
            $unit = UnitQuantity::all();
            if ($unit->isEmpty()) {
                $data = [
                    'message' => 'Unidades de medida inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Unidades de medida encontradas',
                'data' => $unit,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las unidades de medida',
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
                'required','string','max:50',
                Rule::unique('unit_quantities', 'name') 
            ],
            'abbreviation' => [
                'required','string','max:10',
                Rule::unique('unit_quantities', 'abbreviation') 
            ],

        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'abbreviation.required' => 'La abreviatura es obligatoria',
            'abbreviation.string' => 'La abreviatura debe ser una cadena de texto.',
            'abbreviation.max' => 'La abreviatura no puede tener más de 10 carácteres.',
            'abbreviation.unique' => 'La abreviatura ya existe.'
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
            $unit = UnitQuantity::create($validator->validated());
            if (!$unit) {
                $data = [
                    'message' => 'Error al crear la unidad de medida',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Unidad de medida creada',
                'data' => $unit,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la unidad de medida',
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
        $unit = UnitQuantity::find($id);
        if(!$unit){
            $data = [
                'message' => 'Unidad de medida no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Unidad de medida encontrada',
            'data' => $unit,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $units = UnitQuantity::find($id);
        if(!$units){
            $data = [
                'message' => 'Unidad de medida no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => [
                'required','string','max:50',
                Rule::unique('unit_quantities', 'name')->ignore($units->id)
            ],
            'abbreviation' => [
                'required','string','max:10',
                Rule::unique('unit_quantities', 'abbreviation')->ignore($units->id)
            ],

        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'abbreviation.required' => 'La abreviatura es obligatoria',
            'abbreviation.string' => 'La abreviatura debe ser una cadena de texto.',
            'abbreviation.max' => 'La abreviatura no puede tener más de 10 carácteres.',
            'abbreviation.unique' => 'La abreviatura ya existe.'
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
        $units->update($validator->validated());
        $data = [
            'message' => 'Unidad de medida actualizada',
            'data' => $units,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $units = UnitQuantity::find($id);
        if(!$units){
            $data = [
                'message' => 'Unidad de medida no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $units -> delete();

        $data = [
            'message' => 'Unidad de medida eliminada',
            'data' => $units,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }
    public function updatePartial(Request $request, $id){
        $units = UnitQuantity::find($id);
        if(!$units){
            $data = [
                'message' => 'Unidad de medida no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'string','max:50',
                Rule::unique('unit_quantities', 'name')->ignore($units->id)
            ],
            'abbreviation' => [
                'string','max:10',
                Rule::unique('unit_quantities', 'abbreviation')->ignore($units->id)
            ],

        ], [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'abbreviation.string' => 'La abreviatura debe ser una cadena de texto.',
            'abbreviation.max' => 'La abreviatura no puede tener más de 10 carácteres.',
            'abbreviation.unique' => 'La abreviatura ya existe.'
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
            $units -> name = $request -> name;
            $updatedFields['name'] = $request->name;
        }
        if($request -> has('abbreviation')){
            $units -> abbreviation = $request -> abbreviation;
            $updatedFields['abbreviation'] = $request->abbreviation;
        }
        $units -> save();

        $data = [
            'message' => 'Unidad de medida actualizada',
            'data' => $updatedFields,
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
