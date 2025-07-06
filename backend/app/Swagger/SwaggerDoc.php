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
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor principal"
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
