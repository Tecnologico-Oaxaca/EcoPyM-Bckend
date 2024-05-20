<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $comments = Comment::all();
    
            if ($comments->isEmpty()) {
                $data = [
                    'message' => 'Comentarios inexistentes',
                    'data' => null,
                    'status' => Response::HTTP_NOT_FOUND,
                ];
                return response()->json($data, Response::HTTP_NOT_FOUND);
            }

            $data = [
                'message' => 'Commentarios encontrados',
                'data' => $comments,
                'status' => Response::HTTP_OK,
            ];
            return response()->json($data, Response::HTTP_OK);
    
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al obtener los comentarios',
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
            'title' => [
                'required','string','max:100'
            ],
            'body' => [
                'required'
            ],
            'score' => [
                'required','integer','between:1,5'
            ],
            'user_id' => [
                'required', 'exists:users,id' 
            ],

        ], [
            'title.required' => 'Titulo es requqerido',
            'title.string' => 'Titulo debe ser un string',
            'title.max' => 'Titulo debe ser menor a 100 caracteres',
            'body.required' => 'Comentario es requqerido',
            'score.required' => 'Puntuacion es requerida',
            'score.integer' => 'Puntuacion debe ser un numero entero',
            'score.between' => 'Puntuacion debe estar entre 1 y 5',
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'user_id.exists' => 'El ID del usuario no es válido.',

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
            $comments = Comment::create($validator->validated());
            if (!$comments) {
                $data = [
                    'message' => 'Error al crear el comentario',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ];
                return response()->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $data = [
                'message' => 'Cometario creada',
                'data' => $comments,
                'status' => Response::HTTP_CREATED,
            ];
            return response()->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $data = [
                'message' => 'Error al crear el comentario'. $e->getMessage(),
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
        $comments = Comment::find($id);

        if(!$comments){
            $data = [
                'message' => 'Comentario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND 
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $data = [
            'message' => 'Comentario encontrado',
            'data' => $comments,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $comments = Comment::find($id);
        if(!$comments){
            $data = [
                'message' => 'Comentario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'title' => [
                'required','string','max:100'
            ],
            'body' => [
                'required'
            ],
            'score' => [
                'required','integer','between:1,5'
            ],
            'user_id' => [
                'required', 'exists:users,id' 
            ],

        ], [
            'title.required' => 'Titulo es requqerido',
            'title.string' => 'Titulo debe ser un string',
            'title.max' => 'Titulo debe ser menor a 100 caracteres',
            'body.required' => 'Comentario es requqerido',
            'score.required' => 'Puntuacion es requerida',
            'score.integer' => 'Puntuacion debe ser un numero entero',
            'score.between' => 'Puntuacion debe estar entre 1 y 5',
            'user_id.required' => 'El ID del usuario es obligatorio.',
            'user_id.exists' => 'El ID del usuario no es válido.',

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
        $comments->update($validator->validated());

        $data = [
            'message' => 'Comentario actualizado',
            'data' => $comments,
            'status' => Response::HTTP_OK,
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $comments = Comment::find($id);
        if(!$comments){
            $data = [
                'message' => 'Comentario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND,
            ];
            return response() -> json($data,Response::HTTP_NOT_FOUND);
        }

        $comments -> delete();

        $data = [
            'message' => 'Comentario eliminado',
            'data' => $comments,
            'status' => Response::HTTP_OK
        ];
        return response() -> json($data,Response::HTTP_OK);
    }

    public function updatePartial(Request $request, $id){
        $comment = Comment::find($id);
        if (!$comment) {
            $data = [
                'message' => 'Comentario no encontrado',
                'data' => null,
                'status' => Response::HTTP_NOT_FOUND
            ];
            return response()->json($data, Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'title' => [
                'string', 'max:100'
            ],
            'body' => [
                'string'
            ],
            'score' => [
                'integer', 'between:1,5'
            ],
            'user_id' => [
                'exists:users,id'
            ],
        ], [
            'title.string' => 'El título debe ser un string.',
            'title.max' => 'El título debe ser menor a 100 caracteres.',
            'body.string' => 'El comentario debe ser un string.',
            'score.integer' => 'La puntuación debe ser un número entero.',
            'score.between' => 'La puntuación debe estar entre 1 y 5.',
            'user_id.exists' => 'El ID del usuario no es válido.',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'data' => null,
                'status' => Response::HTTP_BAD_REQUEST
            ];
            return response()->json($data, Response::HTTP_BAD_REQUEST);
        }

        $updatedFields = [];

        if ($request->has('title')) {
            $comment->title = $request->title;
            $updatedFields['title'] = $request->title;
        }
        if ($request->has('body')) {
            $comment->body = $request->body;
            $updatedFields['body'] = $request->body;
        }
        if ($request->has('score')) {
            $comment->score = $request->score;
            $updatedFields['score'] = $request->score;
        }
        if ($request->has('user_id')) {
            $comment->user_id = $request->user_id;
            $updatedFields['user_id'] = $request->user_id;
        }

        $comment->save();

        $data = [
            'message' => 'Comentario actualizado',
            'data' => $updatedFields,
            'status' => Response::HTTP_OK
        ];
        return response()->json($data, Response::HTTP_OK);
    }
}
