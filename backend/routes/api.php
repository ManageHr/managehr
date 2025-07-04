<?php


use App\Http\Controllers\Api\areaController;
use App\Http\Controllers\api\categoriaHasUsuarioController;
use App\Http\Controllers\Api\categoriaVacantesController;
use App\Http\Controllers\api\contratoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\rolController;
use App\Http\Controllers\Api\generoController;
use App\Http\Controllers\Api\NacionalidadController;
use App\Http\Controllers\Api\usuarioController;
use App\Http\Controllers\Api\epsController;
use App\Http\Controllers\Api\estadoCivilController;
use App\Http\Controllers\api\estudiosController;
use App\Http\Controllers\api\experienciaLaboralController;
use App\Http\Controllers\Api\hojasvidaController;
use App\Http\Controllers\Api\incapacidadController;
use App\Http\Controllers\Api\pazysalvoController;
use App\Http\Controllers\Api\vacacioneController;
use App\Http\Controllers\Api\usuarioshasrolController;
use App\Http\Controllers\Api\tipopermisosController;
use App\Http\Controllers\Api\permisosController;
use App\Http\Controllers\Api\postulacionesController;
use App\Http\Controllers\Api\pensionesController;
use App\Http\Controllers\Api\tipohorasController;
use App\Http\Controllers\Api\horasextraController;
use App\Http\Controllers\Api\VacantesUserController;
use App\Http\Controllers\Api\MisPostulacionesController;
use App\Http\Controllers\Api\jefePersonalController;
use App\Http\Controllers\Api\VacacionesJefeController;

use App\Http\Controllers\Api\RolPermisoController;
use App\Http\Controllers\api\tipoContratoController;
use App\Http\Controllers\Api\tipodocumentoController;
use App\Http\Controllers\Api\trazabilidadController;
use App\Http\Controllers\api\vacantesController;
use App\Http\Controllers\api\vacantesHasPostulacionesController;

use App\Http\Controllers\Api\formincapacidadController;
use App\Http\Controllers\Api\formvacationController;
use App\Http\Controllers\Api\FormHorasController;
use App\Http\Controllers\Api\HojasvidahasestudiosController;
use App\Http\Controllers\Api\HojasvidahasexperienciaController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\notificacionesController;

Route::middleware('auth:api')->post('/rols/{rol}/permisos', [RolPermisoController::class, 'asignarPermisos']);




Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verificar-numdoc-usuario', [AuthController::class, 'verificarNumDocYUsuario']);
Route::get('/verificar-user', [AuthController::class, 'verificarExistencia']);

Route::middleware('auth:api')->group(function () {

    Route::get('/auth/rolcinco', [AuthController::class, 'rolcinco']);

    Route::post('/rols/{rolId}/permisos', [RolPermisoController::class, 'asignarPermisos']);
    Route::get('/rols/{rolId}/permisos', [RolPermisoController::class, 'obtenerPermisos']);

    Route::get('/auth', [AuthController::class, 'index']);
    Route::get('/auth/{id}', [AuthController::class, 'show']);
    Route::get('/users', [AuthController::class, 'indexConRoles']);
    Route::patch('/auth/rol/{id}', [AuthController::class, 'updatePartial']);






    Route::delete('/login/{id}', [AuthController::class, 'destroy']);
    // Rutas de ROL
    Route::get('/rols', [rolController::class, 'index']);
    Route::post('/rols', [rolController::class, 'store']);
    Route::put('/rols/{id}', [rolController::class, 'update']);
    Route::get('/rols/{id}', [rolController::class, 'show']);
    Route::patch('/rols/{id}', [rolController::class, 'updatePartial']);
    Route::delete('/rols/{id}', [rolController::class, 'destroy']);

    Route::get('/area', [areaController::class, 'index']);
    Route::post('/area', [areaController::class, 'store']);
    Route::put('/area/{id}', [areaController::class, 'update']);
    Route::get('/area/{id}', [areaController::class, 'show']);
    Route::get('/area-nombre/{id}', [areaController::class, 'showNombre']);
    Route::patch('/area/{id}', [areaController::class, 'updatePartial']);
    Route::delete('/area/{id}', [areaController::class, 'destroy']);
    // Rutas de GÉNERO
    Route::get('/genero', [generoController::class, 'index']);
    Route::post('/genero', [generoController::class, 'store']);
    Route::put('/genero/{idGenero}', [generoController::class, 'update']);
    Route::get('/genero/{id}', [generoController::class, 'show']);
    Route::patch('/genero/{id}', [generoController::class, 'updatePartial']);
    Route::delete('/genero/{id}', [generoController::class, 'destroy']);

    // Rutas de NACIONALIDAD
    Route::get('/nacionalidad', [NacionalidadController::class, 'index']);
    Route::post('/nacionalidad', [NacionalidadController::class, 'store']);
    Route::put('/nacionalidad/{idGenero}', [NacionalidadController::class, 'update']);
    Route::get('/nacionalidad/{id}', [NacionalidadController::class, 'show']);
    Route::patch('/nacionalidad/{id}', [NacionalidadController::class, 'updatePartial']);
    Route::delete('/nacionalidad/{id}', [NacionalidadController::class, 'destroy']);

    // Rutas de EPS
    Route::get('/epss', [epsController::class, 'index']);
    Route::post('/epss', [epsController::class, 'store']);
    Route::put('/epss/{id}', [epsController::class, 'update']);
    Route::get('/epss/{id}', [epsController::class, 'show']);
    Route::patch('/epss/{id}', [epsController::class, 'updatePartial']);
    Route::delete('/epss/{id}', [epsController::class, 'destroy']);

    // Rutas de ESTADO CIVIL
    Route::get('/estadocivil', [EstadoCivilController::class, 'index']);
    Route::post('/estadocivil', [EstadoCivilController::class, 'store']);
    Route::put('/estadocivil/{id}', [EstadoCivilController::class, 'update']);
    Route::get('/estadocivil/{id}', [EstadoCivilController::class, 'show']);
    Route::patch('/estadocivil/{id}', [EstadoCivilController::class, 'updatePartial']);
    Route::delete('/estadocivil/{id}', [EstadoCivilController::class, 'destroy']);

    // Rutas de HOJAS DE VIDA
    Route::get('/hojasvida', [hojasvidaController::class, 'index']);
    Route::post('/hojasvida', [hojasvidaController::class, 'store']);
    Route::get('/hojasvida/{id}', [hojasvidaController::class, 'show']);
    Route::patch('/hojasvida/{id}', [hojasvidaController::class, 'updatePartial']);
    Route::delete('/hojasvida/{id}', [hojasvidaController::class, 'destroy']);
    Route::get('/hojasvida/documento/{numDocumento}', [hojasvidaController::class, 'buscarPorDocumento']);
    Route::put('/hojasvida/{id}', [hojasvidaController::class, 'update']);
    Route::get('estudios/hoja/{idHojaDeVida}', [HojasvidahasestudiosController::class, 'buscarPorHojaDeVida']);
    Route::delete('/hojasvidahasestudios/{id}', [HojasvidahasestudiosController::class, 'destroy']);
    Route::delete('/hojasvidahasexperiencia/{id}', [HojasvidahasexperienciaController::class, 'destroy']);
    Route::post('/experiencia-con-archivo', [experienciaLaboralController::class, 'storeConArchivo']);

    // Antes (esto apunta mal la ruta):
    Route::post('/experiencia', [HojasvidahasexperienciaController::class, 'store']);

    // ✅ Después:
    Route::post('/experiencia', [experienciaLaboralController::class, 'store']);

    // Ruta para guardar relación hojaDeVida ↔ experiencia
    Route::post('/hojasvidahasexperiencia', [HojasvidahasexperienciaController::class, 'store']);

    Route::get('/hojasvidahasexperiencia/hoja/{idHojaDeVida}', [HojasvidahasexperienciaController::class, 'buscarPorHojaId']);


    Route::get('/usuarios', [usuarioController::class, 'index']);
    Route::post('/usuarios', [usuarioController::class, 'store']);
    Route::put('/usuarios/{id}', [usuarioController::class, 'update']);
    Route::get('/usuarios/{id}', [usuarioController::class, 'show']);
    Route::patch('/usuarios/{id}', [usuarioController::class, 'updatePartial']);
    Route::delete('/usuarios/{id}', [usuarioController::class, 'destroy']);

    Route::get('/usuarios/reporte/all', [usuarioController::class, 'reporteRoles']);


    Route::get('/verificar-usuario', [usuarioController::class, 'verificarExistencia']);
    Route::get('/usuarios-con-relaciones', [usuarioController::class, 'obtenerUsuariosConRelaciones']);
    Route::get('/usuarios/documento/{numDocumento}', [UsuarioController::class, 'obtenerUsuarioPorDocumento']);

    Route::get('/incapacidad', [incapacidadController::class, 'index']);
    Route::post('/incapacidad', [incapacidadController::class, 'store']);
    Route::put('/incapacidad/{id}', [incapacidadController::class, 'update']);
    Route::get('/incapacidad/{id}', [incapacidadController::class, 'show']);
    Route::patch('/incapacidad/{id}/actualizar', [incapacidadController::class, 'updatePartial']);
    Route::delete('/incapacidad/{id}', [incapacidadController::class, 'destroy']);

    Route::get('/pazysalvo', [pazysalvoController::class, 'index']);
    Route::post('/pazysalvo', [pazysalvoController::class, 'store']);
    Route::put('/pazysalvo/{id}', [pazysalvoController::class, 'update']);
    Route::get('/pazysalvo/{id}', [pazysalvoController::class, 'show']);
    Route::patch('/pazysalvo/{id}', [pazysalvoController::class, 'updatePartial']);
    Route::delete('/pazysalvo/{id}', [pazysalvoController::class, 'destroy']);

    Route::get('/vacaciones', [vacacioneController::class, 'index']);
    Route::post('/vacaciones', [vacacioneController::class, 'store']);
    Route::put('/vacaciones/{id}', [vacacioneController::class, 'update']);
    Route::get('/vacaciones/{id}', [vacacioneController::class, 'show']);
    Route::patch('/vacaciones/{id}', [vacacioneController::class, 'updatePartial']);
    Route::delete('/vacaciones/{id}', [vacacioneController::class, 'destroy']);
    Route::put('/vacaciones/{id}/estado', [VacacioneController::class, 'actualizarEstado']);



    Route::get('/usuarioshasrol', [usuarioshasrolController::class, 'index']);
    Route::post('/usuarioshasrol', [usuarioshasrolController::class, 'store']);
    Route::put('/usuarioshasrol/{id}', [usuarioshasrolController::class, 'update']);
    Route::get('/usuarioshasrol/{id}', [usuarioshasrolController::class, 'show']);
    Route::patch('/usuarioshasrol/{id}', [usuarioshasrolController::class, 'updatePartial']);
    Route::delete('/usuarioshasrol/{id}', [usuarioshasrolController::class, 'destroy']);


    Route::get('/tipopermisos', [tipopermisosController::class, 'index']);
    Route::post('/tipopermisos', [tipopermisosController::class, 'store']);
    Route::put('/tipopermisos/{id}', [tipopermisosController::class, 'update']);
    Route::get('/tipopermisos/{id}', [tipopermisosController::class, 'show']);
    Route::patch('/tipopermisos/{id}', [tipopermisosController::class, 'updatePartial']);
    Route::delete('/tipopermisos/{id}', [tipopermisosController::class, 'destroy']);

    Route::get('/permisos', [permisosController::class, 'index']);
    Route::post('/permisos', [permisosController::class, 'store']);
    Route::put('/permisos/{id}', [permisosController::class, 'update']);
    Route::get('/permisos/{id}', [permisosController::class, 'show']);
    Route::patch('/permisos/{id}', [permisosController::class, 'updatePartial']);
    Route::delete('/permisos/{id}', [permisosController::class, 'destroy']);

    Route::get('/postulaciones', [postulacionesController::class, 'index']);
    Route::post('/postulaciones', [postulacionesController::class, 'store']);
    Route::put('/postulaciones/{id}', [postulacionesController::class, 'updateStatus']);
    Route::get('/postulaciones/{id}', [postulacionesController::class, 'show']);
    Route::patch('/postulaciones/{id}', [postulacionesController::class, 'updatePartial']);
    Route::delete('/postulaciones/{id}', [postulacionesController::class, 'destroy']);
    Route::get('/postulaciones/buscar/vacante/{vacantesId}', [PostulacionesController::class, 'searchByVacantesId']);
    Route::get('/postulaciones/reporte/vacante', [postulacionesController::class, 'porVacante']);
    Route::get('/postulaciones/reporte/estado', [postulacionesController::class, 'porEstado']);
    Route::get('/postulaciones/reporte/internos', [postulacionesController::class, 'porEmpleado']);


    Route::get('/pensiones', [pensionesController::class, 'index']);
    Route::post('/pensiones', [pensionesController::class, 'store']);
    Route::put('/pensiones/{id}', [pensionesController::class, 'update']);
    Route::get('/pensiones/{id}', [pensionesController::class, 'show']);
    Route::patch('/pensiones/{id}', [pensionesController::class, 'updatePartial']);
    Route::delete('/pensiones/{id}', [pensionesController::class, 'destroy']);


    Route::get('/tipohoras', [tipohorasController::class, 'index']);
    Route::post('/tipohoras', [tipohorasController::class, 'store']);
    Route::put('/tipohoras/{id}', [tipohorasController::class, 'update']);
    Route::get('/tipohoras/{id}', [tipohorasController::class, 'show']);
    Route::patch('/tipohoras/{id}', [tipohorasController::class, 'updatePartial']);
    Route::delete('/tipohoras/{id}', [tipohorasController::class, 'destroy']);

    Route::get('/categoriavacantes', [categoriaVacantesController::class, 'index']);
    Route::post('/categoriavacantes', [categoriaVacantesController::class, 'store']);
    Route::put('/categoriavacantes/{id}', [categoriaVacantesController::class, 'update']);
    Route::get('/categoriavacantes/{id}', [categoriaVacantesController::class, 'show']);
    Route::patch('/categoriavacantes/{id}', [categoriaVacantesController::class, 'updatePartial']);
    Route::delete('/categoriavacantes/{id}', [categoriaVacantesController::class, 'destroy']);

    Route::get('/vacantes', [vacantesController::class, 'index']);
    Route::post('/vacantes', [vacantesController::class, 'store']);
    Route::put('/vacantes/{id}', [vacantesController::class, 'update']);
    Route::get('/vacantes/{id}', [vacantesController::class, 'show']);
    Route::patch('/vacantes/{id}', [vacantesController::class, 'updatePartial']);
    Route::delete('/vacantes/{id}', [vacantesController::class, 'destroy']);

    Route::get('/vacantesuser', [VacantesUserController::class, 'index']);
    Route::post('/vacantesuser', [VacantesUserController::class, 'store']);

    Route::get('/contrato', [contratoController::class, 'index']);
    Route::patch('/contrato/{id}/actualizar', [contratoController::class, 'updatePartial']);
    Route::post('/contrato', [contratoController::class, 'store']);
    Route::put('/contrato/{id}', [contratoController::class, 'update']);
    Route::get('/contrato/{id}', [contratoController::class, 'show']);
    Route::patch('/contrato/{id}', [contratoController::class, 'updatePartial']);
    Route::delete('/contrato/{id}', [contratoController::class, 'destroy']);
    Route::get('/contrato/reporte/area', [contratoController::class, 'obtenerContratosConArea']);


    Route::get('/tipocontrato', [tipoContratoController::class, 'index']);
    Route::post('/tipocontrato', [tipoContratoController::class, 'store']);
    Route::put('/tipocontrato/{id}', [tipoContratoController::class, 'update']);
    Route::get('/tipocontrato/{id}', [tipoContratoController::class, 'show']);
    Route::patch('/tipocontrato/{id}', [tipoContratoController::class, 'updatePartial']);
    Route::delete('/tipocontrato/{id}', [tipoContratoController::class, 'destroy']);

    Route::get('/estudios', [estudiosController::class, 'index']);
    Route::post('/estudios', [estudiosController::class, 'store']);
    Route::put('/estudios/{id}', [estudiosController::class, 'update']);
    Route::get('/estudios/{id}', [estudiosController::class, 'show']);
    Route::patch('/estudios/{id}', [estudiosController::class, 'updatePartial']);
    Route::delete('/estudios/{id}', [estudiosController::class, 'destroy']);
    Route::post('/estudios/buscar', [estudiosController::class, 'buscarPorCampos']);

    Route::get('/explaboral', [experienciaLaboralController::class, 'index']);
    Route::post('/experiencialaboral', [experienciaLaboralController::class, 'store']);
    Route::put('/explaboral/{id}', [experienciaLaboralController::class, 'update']);
    Route::get('/explaboral/{id}', [experienciaLaboralController::class, 'show']);
    Route::patch('/explaboral/{id}', [experienciaLaboralController::class, 'updatePartial']);
    Route::delete('/explaboral/{id}', [experienciaLaboralController::class, 'destroy']);

    Route::get('/tipodocumento', [tipodocumentoController::class, 'index']);
    Route::post('/tipodocumento', [tipodocumentoController::class, 'store']);
    Route::put('/tipodocumento/{id}', [tipodocumentoController::class, 'update']);
    Route::get('/tipodocumento/{id}', [tipodocumentoController::class, 'show']);
    Route::patch('/tipodocumento/{id}', [tipodocumentoController::class, 'updatePartial']);
    Route::delete('/tipodocumento/{id}', [tipodocumentoController::class, 'destroy']);

    Route::get('/vachaspos', [vacantesHasPostulacionesController::class, 'index']);
    Route::post('/vachaspos', [vacantesHasPostulacionesController::class, 'store']);
    Route::put('/vachaspos/{id}', [vacantesHasPostulacionesController::class, 'update']);
    Route::get('/vachaspos/{id}', [vacantesHasPostulacionesController::class, 'show']);
    Route::patch('/vachaspos/{id}', [vacantesHasPostulacionesController::class, 'updatePartial']);
    Route::delete('/vachaspos/{id}', [vacantesHasPostulacionesController::class, 'destroy']);

    Route::get('/catvachasusu', [categoriaHasUsuarioController::class, 'index']);
    Route::post('/catvachasusu', [categoriaHasUsuarioController::class, 'store']);
    Route::put('/catvachasusu/{id}', [categoriaHasUsuarioController::class, 'update']);
    Route::get('/catvachasusu/{id}', [categoriaHasUsuarioController::class, 'show']);
    Route::patch('/catvachasusu/{id}', [categoriaHasUsuarioController::class, 'updatePartial']);
    Route::delete('/catvachasusu/{id}', [categoriaHasUsuarioController::class, 'destroy']);

    Route::get('/horasextra', [horasextraController::class, 'index']);
    Route::post('/horasextra', [horasextraController::class, 'store']);
    Route::put('/horasextra/{id}', [horasextraController::class, 'update']);
    Route::get('/horasextra/{id}', [horasextraController::class, 'show']);
    Route::patch('/horasextra/{id}', [horasextraController::class, 'updatePartial']);
    Route::delete('/horasextra/{id}', [horasextraController::class, 'destroy']);

    Route::get('/tipos-horas', [tipohorasController::class, 'index']);

    Route::middleware('auth:api')->get('/profile', [HomeController::class, 'getProfile']);
    Route::middleware('auth:api')->put('/profile/update', [HomeController::class, 'updateProfile']);

    Route::middleware('auth:api')->get('/mis-postulaciones', [MisPostulacionesController::class, 'index']);
    Route::middleware('auth:api')->post('/postulaciones', [PostulacionesController::class, 'store']);
    Route::put('/postulaciones/estado/{id}', [App\Http\Controllers\Api\PostulacionesController::class, 'updateStatus']);


    Route::prefix('notificaciones')->group(function () {
        Route::get('/', [NotificacionesController::class, 'index']);
        Route::post('/', [NotificacionesController::class, 'store']);
        Route::get('/{id}', [NotificacionesController::class, 'show']);
        Route::put('/{id}', [NotificacionesController::class, 'update']);
        Route::delete('/{id}', [NotificacionesController::class, 'destroy']);
        Route::put('/{id}/estado',[NotificacionesController::class, 'actualizarEstado']);
    });
 

    Route::get('/trazabilidad', [trazabilidadController::class, 'index']);
    Route::post('/trazabilidad', [trazabilidadController::class, 'store']);
    Route::put('/trazabilidad/{id}', [trazabilidadController::class, 'update']);
    Route::get('/trazabilidad/{id}', [trazabilidadController::class, 'show']);
    Route::patch('/trazabilidad/{id}', [trazabilidadController::class, 'updatePartial']);
    Route::delete('/trazabilidad/{id}', [trazabilidadController::class, 'destroy']);

    // Solicitudes de Vacaciones
    Route::get('/solicitudes-vacaciones-con-archivo', [formvacationController::class, 'index']);
    Route::post('/solicitudes-vacaciones-con-archivo', [formvacationController::class, 'store']);

    // Solicitudes de Incapacidades
    Route::get('/solicitudes-incapacidades', [formincapacidadController::class, 'index']);
    Route::post('/solicitudes-incapacidades', [formincapacidadController::class, 'store']);

    Route::middleware('auth:api')->get('/hoja-de-vida', [HojasvidaController::class, 'index']);

    // Solicitudes de Horas Extra
    Route::get('/horas-extra', [FormHorasController::class, 'index']);
    Route::post('/horas-extra', [FormHorasController::class, 'store']);

    // Obtener contrato del usuario
    Route::get('/contrato-usuario/{numDocumento}', [ContratoController::class, 'buscarPorDocumento']);

    // hojasvidahasestudios
    Route::get('/hojasvidahasestudios', [HojasvidahasestudiosController::class, 'index']);
    Route::post('/hojasvidahasestudios', [HojasvidahasestudiosController::class, 'store']);
    Route::post('/hojasvidahasestudios/{id}', [HojasvidahasestudiosController::class, 'update']);
    Route::get('/hojasvidahasestudios/{id}', [HojasvidahasestudiosController::class, 'show']);
    Route::delete('/hojasvidahasestudios/{id}', [HojasvidahasestudiosController::class, 'destroy']);
    Route::get('/hojasvidahasestudios/por-hoja/{idHojaDeVida}', [HojasvidahasestudiosController::class, 'buscarPorHojaDeVida']);
    Route::get('hojasvidahasestudios/documento/{numDocumento}', [HojasvidahasestudiosController::class, 'buscarPorDocumento']);
    Route::get('/hojasvidahasestudios/descargar/{id}', [HojasvidahasestudiosController::class, 'descargarArchivo']);

    // hojasvidahasexperiencia
    Route::prefix('hojasvidahasexperiencias')->group(function () {
        Route::get('/', [HojasvidahasexperienciaController::class, 'index']);
        Route::post('/', [HojasvidahasexperienciaController::class, 'store']);
        Route::get('/{id}', [HojasvidahasexperienciaController::class, 'show']);
        Route::put('/{id}', [HojasvidahasexperienciaController::class, 'update']);
        Route::patch('/{id}', [HojasvidahasexperienciaController::class, 'updatePartial']);
        Route::delete('/{id}', [HojasvidahasexperienciaController::class, 'destroy']);
        Route::get('/documento/{numDocumento}', [HojasvidahasexperienciaController::class, 'buscarPorDocumento']);
        Route::get('/descargar/{id}', [HojasvidahasexperienciaController::class, 'descargarArchivo']);
    });

    Route::get('jefe-personal/empleados/{jefeId}', [JefePersonalController::class, 'empleadosPorJefe']);

    // Rutas para Vacaciones del Jefe de Personal
    Route::prefix('solicitudes-vacaciones-jefe')->group(function () {
        Route::get('/', [VacacionesJefeController::class, 'obtenerSolicitudesVacaciones']);
        Route::get('/estadisticas', [VacacionesJefeController::class, 'obtenerEstadisticas']);
        Route::get('/{id}', [VacacionesJefeController::class, 'obtenerSolicitud']);
        Route::put('/{id}/aprobar', [VacacionesJefeController::class, 'aprobarSolicitud']);
        Route::put('/{id}/rechazar', [VacacionesJefeController::class, 'rechazarSolicitud']);
    });
});


Route::middleware('auth:api')->group(function () {});

Route::get('/vacantesExternos', [vacantesController::class, 'index']);
