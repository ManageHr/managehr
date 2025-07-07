<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hojasvida;
use App\Models\User;
use App\Models\rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuarios;


class usuarioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/usuarios",
     *     summary="Obtener lista de todos los usuarios",
     *     description="Devuelve todos los usuarios registrados con su respectivo rol.",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Usuarios obtenidos correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="usuario", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="primerNombre", type="string", example="Juan"),
     *                 @OA\Property(property="email", type="string", example="juan@example.com"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="rol", type="object",
     *                         @OA\Property(property="nombreRol", type="string", example="Administrador")
     *                     )
     *                 )
     *             )),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     )
     * )
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
    
        /**
     * @OA\Get(
     *     path="/api/verificar-usuario",
     *     summary="Verificar existencia de usuario",
     *     description="Valida si ya existe un usuario registrado con el correo electrónico o número de documento enviado.",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Correo electrónico del usuario",
     *         required=false,
     *         @OA\Schema(type="string", format="email", example="test@example.com")
     *     ),
     *     @OA\Parameter(
     *         name="documento",
     *         in="query",
     *         description="Número de documento del usuario",
     *         required=false,
     *         @OA\Schema(type="string", example="123456789")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultado de la verificación",
     *         @OA\JsonContent(
     *             @OA\Property(property="existe", type="boolean", example=true)
     *         )
     *     )
     * )
     */

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
     * @OA\Post(
     *     path="/api/usuarios",
     *     summary="Registrar nuevo usuario",
     *     description="Crea un nuevo usuario con sus datos personales y lo asocia con un usuario base y su rol.",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"numDocumento", "primerNombre", "primerApellido", "password", "fechaNac", "contactoEmergencia", "numContactoEmergencia", "email", "direccion", "telefono", "nacionalidadId", "epsCodigo", "generoId", "tipoDocumentoId", "estadoCivilId", "pensionesCodigo", "usersId"},
     *             @OA\Property(property="numDocumento", type="string", example="123456789"),
     *             @OA\Property(property="primerNombre", type="string", example="Ana"),
     *             @OA\Property(property="segundoNombre", type="string", example="María"),
     *             @OA\Property(property="primerApellido", type="string", example="Gómez"),
     *             @OA\Property(property="segundoApellido", type="string", example="Pérez"),
     *             @OA\Property(property="password", type="string", example="secret123"),
     *             @OA\Property(property="fechaNac", type="string", format="date", example="1990-05-14"),
     *             @OA\Property(property="numHijos", type="integer", example=2),
     *             @OA\Property(property="contactoEmergencia", type="string", example="Carlos"),
     *             @OA\Property(property="numContactoEmergencia", type="string", example="3011234567"),
     *             @OA\Property(property="email", type="string", format="email", example="ana@example.com"),
     *             @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
     *             @OA\Property(property="telefono", type="string", example="3109876543"),
     *             @OA\Property(property="nacionalidadId", type="integer", example=1),
     *             @OA\Property(property="epsCodigo", type="string", example="EPS123"),
     *             @OA\Property(property="generoId", type="integer", example=1),
     *             @OA\Property(property="tipoDocumentoId", type="integer", example=1),
     *             @OA\Property(property="estadoCivilId", type="integer", example=2),
     *             @OA\Property(property="pensionesCodigo", type="string", example="PEN456"),
     *             @OA\Property(property="usersId", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/usuarios/{id}",
     *     summary="Obtener usuario por ID",
     *     description="Devuelve los datos de un usuario específico identificado por su ID.",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/usuarios/{id}",
     *     summary="Eliminar un usuario",
     *     description="Elimina el usuario, su hoja de vida asociada y su cuenta base.",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario y datos relacionados eliminados correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */

    public function destroy($id)
    {
        $usuario = Usuarios::find($id);

        if (!$usuario) {
            return response()->json([
                "mensaje" => "No se encontró el usuario",
                "status" => 404
            ], 404);
        }

        // Eliminar hojas de vida asociadas
        $numDocumento = $usuario->numDocumento;
        Hojasvida::where('usuarioNumDocumento', $numDocumento)->delete();

        // Guardar el usersId
        $usersId = $usuario->usersId;

        // Eliminar usuario
        $usuario->delete();

        // Eliminar user
        $user = User::find($usersId);
        if ($user) {
            $user->delete();
        }

        return response()->json([
            "mensaje" => "Usuario, hoja de vida y cuenta eliminados correctamente",
            "status" => 200
        ], 200);
    }

        /**
     * @OA\Put(
     *     path="/api/usuarios/{id}",
     *     summary="Actualizar un usuario por ID",
     *     description="Modifica completamente los datos de un usuario existente identificado por su ID.",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en los datos enviados"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */


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

        /**
     * @OA\Patch(
     *     path="/api/usuarios/{id}",
     *     summary="Actualizar parcialmente un usuario",
     *     description="Modifica uno o varios campos de un usuario existente identificado por su ID.",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UsuarioInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado parcialmente con éxito"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */

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

        /**
     * @OA\Get(
     *     path="/api/usuarios/documento/{numDocumento}",
     *     summary="Obtener usuario por número de documento",
     *     description="Retorna los datos del usuario, incluyendo todas las relaciones (género, documento, EPS, pensión, etc.) basado en su número de documento.",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         name="numDocumento",
     *         in="path",
     *         description="Número de documento del usuario",
     *         required=true,
     *         @OA\Schema(type="string", example="123456789")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor"
     *     )
     * )
     */

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
                'hojaDeVida' => $usuario,
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

        /**
     * @OA\Get(
     *     path="/api/usuarios/detallado",
     *     summary="Obtener todos los usuarios con información relacionada",
     *     description="Devuelve todos los usuarios registrados junto con sus datos relacionados como género, documento, EPS, rol, etc.",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Usuarios obtenidos con éxito"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener usuarios"
     *     )
     * )
     */

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
        /**
     * @OA\Get(
     *     path="/api/usuarios/jefes",
     *     summary="Obtener jefes de personal",
     *     description="Lista todos los usuarios con el rol de jefe de personal. Devuelve el nombre completo e ID del jefe.",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de jefes obtenida correctamente"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al obtener jefes de personal"
     *     )
     * )
     */

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

        /**
     * @OA\Get(
     *     path="/api/usuarios/reporte-roles",
     *     summary="Obtener reporte de usuarios con sus roles",
     *     description="Devuelve un listado con los usuarios y los datos básicos de su usuario base y su rol asignado.",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="Reporte generado exitosamente"
     *     )
     * )
     */


    public function reporteRoles()
    {
        $usuarios = Usuarios::select('numDocumento', 'primerNombre', 'segundoNombre', 'primerApellido', 'segundoApellido', 'email', 'telefono', 'usersId')
            ->with(['user:id,name,email,rol', 'user.rol:idRol,nombreRol'])
            ->get();

        return response()->json([
            "usuario" => $usuarios,
            "status" => 200
        ]);
    }
}
