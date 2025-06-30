<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuarios;


class usuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Usuarios::with('user.rol')->get();
        $data = [
            "usuario" => $user,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    // ruta: GET /api/verificar-usuario?email=...&documento=...
    public function verificarExistencia(Request $request)
    {
        $email = $request->query('email');
        $documento = $request->query('documento');

        $existe = Usuarios::where('email', $email)
            ->orWhere('numDocumento', $documento)
            ->exists();

        return response()->json(['existe' => $existe]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numDocumento' => 'required|integer|unique:usuarios,numDocumento',
            'primerNombre' => 'required|string|max:30',
            'segundoNombre' => 'nullable|string|max:30',
            'primerApellido' => 'required|string|max:30',
            'segundoApellido' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'fechaNac' => 'required|date',
            'numHijos' => 'nullable|integer|min:0',
            'contactoEmergencia' => 'required|string|max:30',
            'numContactoEmergencia' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'direccion' => 'required|string|max:45',
            'telefono' => 'required|string|max:20',
            'nacionalidadId' => 'required|integer',
            'epsCodigo' => 'required|string',
            'generoId' => 'required|integer',
            'tipoDocumentoId' => 'required|integer',
            'estadoCivilId' => 'required|integer',
            'pensionesCodigo' => 'required|string',
            'usersId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error en la validación de datos del usuario',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        try {
            $usuario = Usuarios::create([
                'numDocumento' => $request->numDocumento,
                'primerNombre' => $request->primerNombre,
                'segundoNombre' => $request->segundoNombre,
                'primerApellido' => $request->primerApellido,
                'segundoApellido' => $request->segundoApellido,
                'password' => bcrypt($request->password),
                'fechaNac' => $request->fechaNac,
                'numHijos' => $request->numHijos,
                'contactoEmergencia' => $request->contactoEmergencia,
                'numContactoEmergencia' => $request->numContactoEmergencia,
                'email' => $request->email,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'nacionalidadId' => $request->nacionalidadId,
                'epsCodigo' => $request->epsCodigo,
                'generoId' => $request->generoId,
                'tipoDocumentoId' => $request->tipoDocumentoId,
                'estadoCivilId' => $request->estadoCivilId,
                'pensionesCodigo' => $request->pensionesCodigo,
                'usersId' => $request->usersId
            ]);

            return response()->json([
                'mensaje' => 'Usuario creado correctamente',
                'usuario' => $usuario,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => 'Error al crear el usuario',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Usuarios::find($id);
        $data = [
            "usuario" => $user,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy($id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            $data = [
                "mensage" => " No se encontro Usuarios",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $usuario->delete();
        $data = [
            "rol" => 'Usuario eliminado',
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function update(Request $request, $id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            $data = [
                "mensage" => " No se encontro Usuario",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [

            'primerNombre' => 'required|string|max:30',
            'segundoNombre' => 'nullable|string|max:30',
            'primerApellido' => 'required|string|max:30',
            'segundoApellido' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'fechaNac' => 'required|date',
            'numHijos' => 'nullable|integer|min:0',
            'contactoEmergencia' => 'required|string|max:30',
            'numContactoEmergencia' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'direccion' => 'required|string|max:45',
            'telefono' => 'required|string|max:20',
            'nacionalidadId' => 'required|integer',
            'epsCodigo' => 'required|string',
            'generoId' => 'required|integer',
            'tipoDocumentoId' => 'required|integer',
            'estadoCivilId' => 'required|integer',
            'pensionesCodigo' => 'required|string',
            'usersId' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $data = [
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }

        $usuario->primerNombre = $request->primerNombre;
        $usuario->segundoNombre = $request->segundoNombre;
        $usuario->primerApellido = $request->primerApellido;
        $usuario->segundoApellido = $request->segundoApellido;
        $usuario->password = $request->password;
        $usuario->fechaNac = $request->fechaNac;
        $usuario->numHijos = $request->numHijos;
        $usuario->contactoEmergencia = $request->contactoEmergencia;
        $usuario->numContactoEmergencia = $request->numContactoEmergencia;
        $usuario->email = $request->email;
        $usuario->direccion = $request->direccion;
        $usuario->telefono = $request->telefono;
        $usuario->nacionalidadId = $request->nacionalidadId;
        $usuario->epsCodigo = $request->epsCodigo;
        $usuario->generoId = $request->generoId;
        $usuario->tipoDocumentoId = $request->tipoDocumentoId;
        $usuario->estadoCivilId = $request->estadoCivilId;
        $usuario->pensionesCodigo = $request->pensionesCodigo;
        $usuario->usersId = $request->usersId;
        try {
            $usuario->save();
            $data = [
                "usuarios" => $usuario,
                "status" => 200
            ];
            return response()->json([$data], 200);
        } catch (\Exception $e) {
            return response()->json([
                "mensaje" => "Error al modificar el usuario",
                "error" => $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    public function updatePartial(Request $request, $id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            $data = [
                "mensage" => " No se encontro Usuario",
                "status" => 404
            ];
            return response()->json([$data], 404);
        }
        $validator = Validator::make($request->all(), [

            'primerNombre' => 'string|max:30',
            'segundoNombre' => 'nullable|string|max:30',
            'primerApellido' => 'string|max:30',
            'segundoApellido' => 'nullable|string|max:30',
            'password' => 'string|min:6',
            'fechaNac' => 'date',
            'numHijos' => 'nullable|integer|min:0',
            'contactoEmergencia' => 'string|max:30',
            'numContactoEmergencia' => 'string|max:20',
            'email' => 'email|max:100',
            'direccion' => 'string|max:45',
            'telefono' => 'string|max:20',
            'nacionalidadId' => 'integer',
            'epsCodigo' => 'string',
            'generoId' => 'integer',
            'tipoDocumentoId' => 'integer',
            'estadoCivilId' => 'integer',
            'pensionesCodigo' => 'string',
            'usersId' => 'integer',
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar Usuario",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json([$data], 400);
        }
        if ($request->has("numDocumento")) {
            $usuario->numDocumento = $request->numDocumento;
        }
        if ($request->has("primerNombre")) {
            $usuario->primerNombre = $request->primerNombre;
        }
        if ($request->has("segundoNombre")) {
            $usuario->segundoNombre = $request->segundoNombre;
        }
        if ($request->has("primerApellido")) {
            $usuario->primerApellido = $request->primerApellido;
        }
        if ($request->has("segundoApellido")) {
            $usuario->segundoApellido = $request->segundoApellido;
        }
        if ($request->has("password")) {
            $usuario->password = $request->password;
        }
        if ($request->has("fechaNac")) {
            $usuario->fechaNac = $request->fechaNac;
        }
        if ($request->has("numHijos")) {
            $usuario->numHijos = $request->numHijos;
        }
        if ($request->has("contactoEmergencia")) {
            $usuario->contactoEmergencia = $request->contactoEmergencia;
        }
        if ($request->has("numContactoEmergencia")) {
            $usuario->numContactoEmergencia = $request->numContactoEmergencia;
        }
        if ($request->has("email")) {
            $usuario->email = $request->email;
        }
        if ($request->has("direccion")) {
            $usuario->direccion = $request->direccion;
        }
        if ($request->has("telefono")) {
            $usuario->telefono = $request->telefono;
        }
        if ($request->has("nacionalidadId")) {
            $usuario->nacionalidadId = $request->nacionalidadId;
        }
        if ($request->has("epsCodigo")) {
            $usuario->epsCodigo = $request->epsCodigo;
        }
        if ($request->has("generoId")) {
            $usuario->generoId = $request->generoId;
        }
        if ($request->has("tipoDocumentoId")) {
            $usuario->tipoDocumentoId = $request->tipoDocumentoId;
        }
        if ($request->has("estadoCivilId")) {
            $usuario->estadoCivilId = $request->estadoCivilId;
        }
        if ($request->has("pensionesCodigo")) {
            $usuario->pensionesCodigo = $request->pensionesCodigo;
        }
        if ($request->has("usersId")) {
            $usuario->usersId = $request->usersId;
        }
        $usuario->save();
        if ($request->has('userBase')) {
            $user = User::find($request->input('userBase.id'));

            if ($user) {
                $user->email = $request->input('userBase.email');
                $user->rol = $request->input('userBase.rol');
                $user->name = $request->input('userBase.name');
                $user->save();
            }
        }
        $data = [
            "rol" => $usuario,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
    public function obtenerUsuarioPorDocumento($numDocumento)
    {
        try {
            $usuario = Usuarios::with([
                'tipoDocumento',
                'genero',
                'estadoCivil',
                'eps',
                'pensiones',
                'nacionalidad',
                'user.rol'
            ])->where('numDocumento', $numDocumento)->first();

            if (!$usuario) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'usuario' => $usuario,
                'status' => 200,
                'message' => 'Usuario obtenido correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 500,
                'message' => 'Error al obtener el usuario'
            ], 500);
        }
    }

    public function obtenerUsuariosConRelaciones()
    {
        try {
            $usuarios = Usuarios::with([
                'tipoDocumento',
                'genero',
                'estadoCivil',
                'eps',
                'pensiones',
                'nacionalidad',
                'user.rol'
            ])->get();

            return response()->json([
                'usuarios' => $usuarios,
                'status' => 200,
                'message' => 'Usuarios obtenidos correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 500,
                'message' => 'Error al obtener usuarios'
            ], 500);
        }
    }
    public function obtenerJefesDePersonal()
    {
        try {
            $jefes = User::where('rol', 2)
                ->join('usuarios', 'users.id', '=', 'usuarios.usersId')
                ->select(
                    'users.id as idJefe',
                    'usuarios.primerNombre',
                    'usuarios.primerApellido'
                )
                ->get()
                ->map(function ($jefe) {
                    return [
                        'idJefe' => $jefe->idJefe,
                        'nombreCompleto' => $jefe->primerNombre . ' ' . $jefe->primerApellido
                    ];
                });

            return response()->json([
                'jefes' => $jefes,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener jefes de personal',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
   

    public function reporteRoles()
    {
        $usuarios = Usuarios::with('user')->get();

        $usuariosConRol = $usuarios->map(function ($usuario) {
            $user = $usuario->user;

            // Si no hay user, devuelve el usuario tal cual
            if (!$user) return $usuario;

            // Verificamos si 'rol' es un número (ID), no una colección
            if (is_numeric($user->rol)) {
                $rol = Rol::find($user->rol);
                if ($rol) {
                    $user->rol = [
                        'idRol' => $rol->idRol,
                        'nombreRol' => $rol->nombreRol,
                    ];
                }
            }

            return $usuario;
        });

        return response()->json([
            "usuario" => $usuariosConRol,
            "status" => 200
        ]);
    }
}
