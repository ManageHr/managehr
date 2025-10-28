<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
        /**
     * @OA\Get(
     *     path="/api/auth",
     *     summary="Listar todos los usuarios base",
     *     description="Devuelve todos los registros de usuarios (tabla base `users`).",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios obtenida correctamente"
     *     )
     * )
     */


    public function index()
    {
        $user = User::all();
        $data = [
            "usuario" => $user,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

        /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Registrar nuevo usuario",
     *     description="Crea un nuevo usuario y genera un token de autenticación JWT.",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "email_confirmation", "password", "password_confirmation", "rol"},
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="email_confirmation", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="123456"),
     *             @OA\Property(property="password_confirmation", type="string", example="123456"),
     *             @OA\Property(property="rol", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado correctamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación"
     *     )
     * )
     */

    public function register(Request $request)
    {
        // Validar campos, incluyendo confirmación de email y contraseña
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|confirmed|unique:users,email',
            'email_confirmation' => 'required|string|email',
            'password' => 'required|string|min:6|confirmed',
            'rol' => 'required',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en el registro.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar coincidencia de confirmaciones manualmente (opcional)
        if (
            $request->email !== $request->email_confirmation ||
            $request->password !== $request->password_confirmation
        ) {
            return response()->json([
                'message' => 'Los campos de confirmación no coinciden.'
            ], 422);
        }

        // Crear usuario (la contraseña se hashea automáticamente en el modelo)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Usar bcrypt para hashear la contraseña
            'rol' => $request->rol
        ]);

        // Generar token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario registrado correctamente 🎉',
            'token' => $token,
            'user' => $user,
            'redirect' => '/directorio' // Para redirigir desde Angular
        ], 201);
    }

        /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Iniciar sesión",
     *     description="Autentica al usuario con correo y contraseña, y retorna un token JWT.",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales incorrectas"
     *     )
     * )
     */


    public function login(Request $request)
    {
        // Extraer las credenciales del request
        $credentials = $request->only('email', 'password');

        // Registrar logs para depuración
        Log::info('Intento de login', ['email' => $request->email ?? 'Correo no proporcionado']);

        // Intentar autenticar al usuario
        if (!$token = JWTAuth::attempt($credentials)) {
            Log::error('Error en login: credenciales incorrectas', ['email' => $request->email ?? 'Correo no proporcionado']);
            return response()->json(['error' => 'Correo o contraseña incorrectos'], 401);
        }

        // Obtener el usuario autenticado
        $user = JWTAuth::user();

        // Si por alguna razón no se genera token, forzar la creación (fallback)
        if (!$token) {
            $token = JWTAuth::fromUser($user);
        }

        // Responder con token y datos de usuario
        return response()->json([
            'user' => $user,
            'token' => $token,
            'redirect' => '/directorio'
        ]);
    }

        /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Obtener usuario autenticado",
     *     description="Retorna los datos del usuario autenticado actualmente.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Usuario autenticado encontrado"
     *     )
     * )
     */


    public function me()
    {
        return response()->json([
            'message' => 'Usuario autenticado con éxito',
            'user' => Auth::user()
        ]);
    }

        /**
     * @OA\Delete(
     *     path="/api/auth/{id}",
     *     summary="Eliminar usuario base",
     *     description="Elimina un registro del usuario en la tabla `users` por ID.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario eliminado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     )
     * )
     */

    public function destroy($id)
    {
        $usuario = User::find($id);
        if (!$usuario) {
            $data = [
                "mensage" => " No se encontro Usuario",
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

        /**
     * @OA\Get(
     *     path="/api/auth/verificar-numdoc-usuario",
     *     summary="Verificar si existe un nombre de usuario o número de documento",
     *     description="Comprueba si el nombre de usuario existe en la tabla `users` o si el número de documento existe en `usuarios`.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="numDocumento", in="query", required=false, @OA\Schema(type="string", example="123456789")),
     *     @OA\Parameter(name="usuario", in="query", required=false, @OA\Schema(type="string", example="admin")),
     *     @OA\Response(
     *         response=200,
     *         description="Resultado de verificación devuelto correctamente"
     *     )
     * )
     */

    public function verificarNumDocYUsuario(Request $request)
    {
        $numDocumento = $request->query('numDocumento');
        $usuario = $request->query('usuario');

        $existeEnUsers = User::where('name', $usuario)->exists();
        $existeEnUsuarios = Usuarios::where('numDocumento', $numDocumento)->exists();

        return response()->json([
            'existeEnUsers' => $existeEnUsers,
            'existeEnUsuarios' => $existeEnUsuarios,
            'existe' => $existeEnUsers || $existeEnUsuarios
        ]);
    }

        /**
     * @OA\Get(
     *     path="/api/auth/verificar-email",
     *     summary="Verificar existencia de correo electrónico",
     *     description="Verifica si el correo ya está registrado en la tabla `users` o en `usuarios`.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string", example="admin@example.com")),
     *     @OA\Response(
     *         response=200,
     *         description="Resultado de verificación devuelto correctamente"
     *     )
     * )
     */

    public function verificarExistencia(Request $request)
    {
        $email = $request->query('email');

        $existeUser = User::where('email', $email)->exists();
        $existeEnUsuarios = Usuarios::where('email', $email)->exists();

        return response()->json([
            'existeEnUsers' => $existeUser,
            'existeEnUsuarios' => $existeEnUsuarios,
            'existe' => $existeUser || $existeEnUsuarios
        ]);
    }

        /**
     * @OA\Get(
     *     path="/api/auth/con-roles",
     *     summary="Listar usuarios con sus roles",
     *     description="Devuelve todos los usuarios con la relación de su rol cargada.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Usuarios con roles obtenidos correctamente"
     *     )
     * )
     */

    public function indexConRoles()
    {
        $usuarios = User::with('roles')->get();

        return response()->json([
            'status' => 200,
            'usuarios' => $usuarios
        ]);
    }

        /**
     * @OA\Patch(
     *     path="/api/auth/{id}",
     *     summary="Actualizar parcialmente un usuario base",
     *     description="Modifica uno o más campos del registro del usuario base (`users`).",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del usuario base",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="admin2"),
     *             @OA\Property(property="email", type="string", example="nuevo@example.com"),
     *             @OA\Property(property="password", type="string", example="nuevo123"),
     *             @OA\Property(property="rol", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado correctamente"
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
        $usuario = User::find($id);
        if (!$usuario) {
            $data = [
                "mensage" => " No se encontro Usuario",
                "status" => 404
            ];
            return response()->json($data, 404);
        }
        $validator = Validator::make($request->all(), [

            'name' => 'string|max:255',
            'email' => 'string|max:255',
            'password' => 'string|max:255',
            'rol' => 'integer'
        ]);
        if ($validator->fails()) {
            $data = [
                "mesaje " => "Error al validar Users",
                "errors" => $validator->errors(),
                "status" => 400
            ];
            return response()->json($data, 400);
        }
        if ($request->has("name")) {
            $usuario->name = $request->name;
        }
        if ($request->has("email")) {
            $usuario->email = $request->email;
        }
        if ($request->has("password")) {
            $usuario->password = $request->password;
        }
        if ($request->has("rol")) {
            $usuario->rol = $request->rol;
        }

        $usuario->save();
        $data = [
            "rol" => $usuario,
            "status" => 200
        ];
        return response()->json($data, 200);
    }

        /**
     * @OA\Get(
     *     path="/api/auth/rol/5",
     *     summary="Obtener usuarios con rol 5",
     *     description="Filtra y retorna todos los usuarios con rol igual a 5.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Usuarios con rol 5 obtenidos correctamente"
     *     )
     * )
     */

    public function rolcinco()
    {
        $usuarios = User::where('rol', 5)->get();

        return response()->json([
            'status' => 200,
            'usuarios' => $usuarios
        ]);
    }

        /**
     * @OA\Get(
     *     path="/api/auth/{id}",
     *     summary="Obtener usuario con rol por ID",
     *     description="Devuelve los datos del usuario junto con su rol asignado.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
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
        $user = User::with('rol')->find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $data = [
            "usuario" => $user,
            "status" => 200
        ];
        return response()->json([$data], 200);
    }
}
