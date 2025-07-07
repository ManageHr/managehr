<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="ManageHR API",
 *     version="1.0.0",
 *     description="Documentación de la API RESTful de ManageHR desarrollada en Laravel con autenticación JWT"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Introduce tu token JWT en el formato: Bearer {token}"
 * )
 */
class SwaggerDoc {}
