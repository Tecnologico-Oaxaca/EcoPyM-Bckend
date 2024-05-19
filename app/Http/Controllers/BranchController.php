<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try {
            $branches = Branch::all();
    
            if ($branches->isEmpty()) {
                $data = [
                    'message' => 'Sucursales inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Sucursales encontradas',
                'data' => $branches->load('mipyme'),
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener las sucursales',
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
            'image' => [
                'nullable', 
            ],
            'open_time' => [
                'required', 'date_format:H:i'
            ],
            'close_time' => [
                'required', 'date_format:H:i','after:open_time'
            ],
            'phone' => [
                'required','digits:10',Rule::unique('branches', 'phone')
            ],
            'state' => [
                'required',
                Rule::unique('branches')->where(function ($query) use ($request) {
                           return $query->where('city', $request->city)
                                        ->where('district', $request->district)
                                        ->where('street', $request->street)
                                        ->where('number', $request->number);
                }),
            ],
            'city' => [
                'required',
            ],
            'district' => [
                'required',
            ],
            'street' => [
                'required',
            ],
            'number' => [
                'required','numeric','min:0','max:99999'
            ],
            'mipyme_id' => [
                'required', 'exists:mipymes,id' 
            ],

        ], [
            'open_time.required' => 'El horario de apertura es obligatorio.',
            'open_time.date_format' => 'El horario de apertura debe estar en el formato correcto (HH:mm).',
            'close_time.required' => 'El horario de cierre es obligatorio.',
            'close_time.date_format' => 'El horario de cierre debe estar en el formato correcto (HH:mm).',
            'close_time.after' => 'El horario de cierre debe ser posterior al horario de apertura.',
            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.digits' => 'El número de teléfono debe tener exactamente 10 dígitos.',
            'phone.unique' => 'El numero ya existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.unique' => 'Una sucursal con esta dirección ya existe.',
            'city.required' => 'El municipio es obligatoria.',
            'district.required' => 'La colonia es obligatorio.',
            'street.required' => 'La calle es obligatoria.',
            'number.required' => 'El número es obligatorio.',
            'number.numeric' => 'El número debe ser un valor numérico.',
            'number.min' => 'El número debe ser un valor positivo.',
            'number.max' => 'El número no puede tener más de 5 dígitos.',
            'mipyme_id.required' => 'El ID de la mipyme es obligatorio.',
            'mipyme_id.exists' => 'El ID de la mipyme no es válido.',

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
            $branches = Branch::create($validator->validated());
            if (!$branches) {
                $data = [
                    'message' => 'Error al crear la sucursal',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Sucursal creada',
                'data' => $branches->load('mipyme'),
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear la sucursal',
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
        $branches = Branch::find($id);

        if(!$branches){
            $data = [
                'message' => 'Sucursal no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Sucursal encontrada',
            'data' => $branches->load('mipyme'),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $branches = Branch::find($id);
        if(!$branches){
            $data = [
                'message' => 'Sucursal no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'image' => [
                'nullable', 
            ],
            'open_time' => [
                'required', 'date_format:H:i'
            ],
            'close_time' => [
                'required', 'date_format:H:i','after:open_time'
            ],
            'phone' => [
                'required','digits:10',Rule::unique('branches', 'phone')->ignore($branches->id)
            ],
            'state' => [
                'required',
                Rule::unique('branches')
                    ->where(function ($query) use ($request, $branches) {
                        return $query->where('city', $request->city)
                                     ->where('district', $request->district)
                                     ->where('street', $request->street)
                                     ->where('number', $request->number)
                                     ->where('id', '<>', $branches->id); 
                    }),
            ],            
            'city' => [
                'required',
            ],
            'district' => [
                'required',
            ],
            'street' => [
                'required',
            ],
            'number' => [
                'required','numeric','min:0','max:99999'
            ],
            'mipyme_id' => [
                'required', 'exists:mipymes,id' 
            ],

        ], [
            'open_time.required' => 'El horario de apertura es obligatorio.',
            'open_time.date_format' => 'El horario de apertura debe estar en el formato correcto (HH:mm).',
            'close_time.required' => 'El horario de cierre es obligatorio.',
            'close_time.date_format' => 'El horario de cierre debe estar en el formato correcto (HH:mm).',
            'close_time.after' => 'El horario de cierre debe ser posterior al horario de apertura.',
            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.digits' => 'El número de teléfono debe tener exactamente 10 dígitos.',
            'phone.unique' => 'El numero ya existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.unique' => 'Una sucursal con esta dirección ya existe.',
            'city.required' => 'El municipio es obligatoria.',
            'district.required' => 'La colonia es obligatorio.',
            'street.required' => 'La calle es obligatoria.',
            'number.required' => 'El número es obligatorio.',
            'number.numeric' => 'El número debe ser un valor numérico.',
            'number.min' => 'El número debe ser un valor positivo.',
            'number.max' => 'El número no puede tener más de 5 dígitos.',
            'mipyme_id.required' => 'El ID de la mipyme es obligatorio.',
            'mipyme_id.exists' => 'El ID de la mipyme no es válido.',

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
        $branches->update($validator->validated());

        $data = [
            'message' => 'Sucursal actualizada',
            'data' => $branches->load('mipyme'),
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $branches = Branch::find($id);
        if(!$branches){
            $data = [
                'message' => 'Sucursal no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $branches -> delete();

        $data = [
            'message' => 'Sucursal eliminada',
            'data' => $branches->load('mipyme'),
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){

        $branches = Branch::find($id);
        if(!$branches){
            $data = [
                'message' => 'Sucursal no encontrada',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'image' => [
                'nullable'
            ],
            'open_time' => [
                'sometimes', 'date_format:H:i'
            ],
            'close_time' => [
                'sometimes', 'date_format:H:i', 'after:open_time'
            ],
            'phone' => [
                'sometimes', 'digits:10', Rule::unique('branches', 'phone')->ignore($branches->id)
            ],
            'state' => [
                'required_with_all:city,district,street,number',
                Rule::unique('branches')
                    ->where(function ($query) use ($request, $branches) {
                        return $query->where('city', $request->city)
                                     ->where('district', $request->district)
                                     ->where('street', $request->street)
                                     ->where('number', $request->number)
                                     ->where('id', '<>', $branches->id); 
                    }),
            ],
            'city' => [
                'required_with_all:state,district,street,number'
            ],
            'district' => [
                'required_with_all:state,city,street,number'
            ],
            'street' => [
                'required_with_all:state,city,district,number'
            ],
            'number' => [
                'required_with_all:state,city,district,street', 'numeric', 'min:0', 'max:99999'
            ],
            'mipyme_id' => [
                'sometimes', 'exists:mipymes,id'
            ],
        ], [
            'open_time.date_format' => 'El horario de apertura debe estar en el formato correcto (HH:mm).',
            'close_time.date_format' => 'El horario de cierre debe estar en el formato correcto (HH:mm).',
            'close_time.after' => 'El horario de cierre debe ser posterior al horario de apertura.',
            'phone.digits' => 'El número de teléfono debe tener exactamente 10 dígitos.',
            'phone.unique' => 'El número ya existe.',
            'state.required_with_all' => 'El campo state es obligatorio cuando city, district, street, y number están presentes.',
            'state.unique' => 'La combinación de dirección ya existe en otra sucursal.',
            'city.required_with_all' => 'El campo city es obligatorio cuando state, district, street, y number están presentes.',
            'district.required_with_all' => 'El campo district es obligatorio cuando state, city, street, y number están presentes.',
            'street.required_with_all' => 'El campo street es obligatorio cuando state, city, district, y number están presentes.',
            'number.required_with_all' => 'El campo number es obligatorio cuando state, city, district, y street están presentes.',
            'number.numeric' => 'El número debe ser un valor numérico.',
            'number.min' => 'El número debe ser un valor positivo.',
            'number.max' => 'El número no puede tener más de 5 dígitos.',
            'mipyme_id.exists' => 'El ID de la mipyme no es válido.',
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

        if($request -> has('image')){
            $branches -> image = $request -> image;
        }
        if($request -> has('open_time')){
            $branches -> open_time = $request -> open_time;
        }
        if($request -> has('close_time')){
            $branches -> close_time = $request -> close_time;
        }
        if($request -> has('phone')){
            $branches -> phone = $request -> phone;
        }
        if ($request->has(['state', 'city', 'district', 'street', 'number'])) {
            $branches->state = $request->state;
            $branches->city = $request->city;
            $branches->district = $request->district;
            $branches->street = $request->street;
            $branches->number = $request->number;
        }
        if($request -> has('mipyme_id')){
            $branches -> mipyme_id = $request -> mipyme_id;
        }
        $branches -> save();

        $data = [
            'message' => 'Sucursal actualizada',
            'data' => $branches->load('mipyme'),
            'status' => RESPONSE::HTTP_OK
        ];
        return response() -> json($data,RESPONSE::HTTP_OK);
    }
}
