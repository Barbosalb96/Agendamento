<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Sistema de Agendamento API",
 *     version="1.0.0",
 *     description="API REST para gerenciamento de agendamentos com autenticação JWT, gestão de dias fechados e QR Code para validação. Esta API permite criar, listar, visualizar e cancelar agendamentos, além de gerenciar dias bloqueados e validar QR Codes.",
 *     termsOfService="http://localhost:8000/terms",
 *
 *     @OA\Contact(
 *         name="Equipe de Desenvolvimento",
 *         email="dev@agendamento.com",
 *         url="https://github.com/seu-usuario/agendamento"
 *     ),
 *
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor de desenvolvimento"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Digite o token Bearer no formato: Bearer {seu-token-jwt}. Para obter um token, faça login através do endpoint /api/login"
 * )
 *
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Operações de login, logout e gerenciamento de senhas"
 * )
 * @OA\Tag(
 *     name="Agendamentos",
 *     description="Operações CRUD para agendamentos - criar, listar, visualizar e cancelar"
 * )
 * @OA\Tag(
 *     name="Gestão de Dias",
 *     description="Gerenciamento de dias bloqueados, feriados e restrições de agendamento"
 * )
 * @OA\Tag(
 *     name="QR Code",
 *     description="Validação de códigos QR para confirmação de agendamentos"
 * )
 */
abstract class Controller
{
    //
}
