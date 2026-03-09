<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="DevLink API",
 *     version="1.0.0",
 *     description="Developer Resource Hub API",
 *     @OA\Contact(email="ardiansyahsulistyo@gmail.com"),
 *     @OA\License(name="MIT")
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Server(url=L5_SWAGGER_CONST_HOST, description="API Server")
 */
abstract class Controller
{

}
