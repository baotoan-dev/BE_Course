<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="My Laravel API",
 *         description="This is the API documentation for the Laravel app."
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000",
 *         description="Local server"
 *     )
 * )
 */
class SwaggerDoc {} // 👈 PHẢI có class để annotation gắn vào
