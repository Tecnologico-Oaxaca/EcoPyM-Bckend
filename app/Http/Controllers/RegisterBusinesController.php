<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Mipyme;
use App\Services\TransferirDatosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RegisterBusinesController extends Controller
{
    protected $transferirDatosService;

    public function __construct(TransferirDatosService $transferirDatosService)
    {
        $this->transferirDatosService = $transferirDatosService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:50', Rule::unique('mipymes', 'name')
            ],
            'email' => [
                'nullable', 'string', 'max:50', 'email', Rule::unique('mipymes', 'email')
            ],
            'image' => [
                'nullable',
            ],
            'business_ids' => [
                'required', 'array'
            ],
            'business_ids.*' => [
                'exists:busines,id'
            ],
            'open_time' => [
                'required', 'date_format:H:i'
            ],
            'close_time' => [
                'required', 'date_format:H:i', 'after:open_time'
            ],
            'phone' => [
                'nullable', 'digits:10', Rule::unique('branches', 'phone')
            ],
            'state' => [
                'required', 
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
                'nullable', 'numeric', 'min:0', 'max:99999'
            ],
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede ser mayor a 50 caracteres.',
            'name.unique' => 'El nombre ya existe.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'El email no es válido.',
            'email.max' => 'El email no puede ser mayor a 50 caracteres.',
            'email.unique' => 'El email ya existe.',
            'business_ids.required' => 'Debe proporcionar al menos un giro comercial',
            'business_ids.*.exists' => 'Uno de los giros comerciales proporcionados no existe',
            'open_time.required' => 'El horario de apertura es obligatorio.',
            'open_time.date_format' => 'El horario de apertura debe estar en el formato correcto (HH:mm).',
            'close_time.required' => 'El horario de cierre es obligatorio.',
            'close_time.date_format' => 'El horario de cierre debe estar en el formato correcto (HH:mm).',
            'close_time.after' => 'El horario de cierre debe ser posterior al horario de apertura.',
            'phone.digits' => 'El número de teléfono debe tener exactamente 10 dígitos.',
            'phone.unique' => 'El numero ya existe.',
            'state.required' => 'El estado es obligatorio.',
            'city.required' => 'El municipio es obligatorio.',
            'district.required' => 'La colonia es obligatorio.',
            'street.required' => 'La calle es obligatoria.',
            'number.numeric' => 'El número debe ser un valor numérico.',
            'number.min' => 'El número debe ser un valor positivo.',
            'number.max' => 'El número no puede tener más de 5 dígitos.',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validación fallida',
                'errors' => $validator->errors(),
                'data' => null,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
            return response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        try {
            $mipymeData = $request->only(['name', 'email', 'image']);
            $mipyme = Mipyme::create($mipymeData);

            if (!$mipyme) {
                throw new \Exception('Error al crear la MIPyME');
            }

            $mipyme->businesses()->sync($request->business_ids);

            $branchData = $request->only([
                'open_time', 'close_time', 'phone', 'state', 'city', 'district', 'street', 'number'
            ]);
            $branchData['mipyme_id'] = $mipyme->id;

            $branch = Branch::create($branchData);

            if (!$branch) {
                throw new \Exception('Error al crear la sucursal');
            }

            DB::commit();

            // Ejecutar la lógica de transferencia de datos después de que la transacción se haya comprometido
            $this->transferirDatosService->transferirDatos($request->business_ids);

            $data = [
                'message' => 'MIPyME y sucursal creadas con éxito',
                'data' => [
                    'mipyme' => $mipyme->load(['businesses', 'branches']),
                    'branch' => $branch,
                    'branch_id' => $branch->id,
                ],
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            $data = [
                'message' => $e->getMessage(),
                'data' => null,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ];
            return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}
