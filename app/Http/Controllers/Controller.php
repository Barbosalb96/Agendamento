<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Sistema de Agendamento API",
 *     version="1.0.0",
 *     description="API para sistema de agendamento com gestão de dias e usuários",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Insira o token Bearer no formato: Bearer {token}"
 * )
 */
abstract class Controller
{
    //
}
