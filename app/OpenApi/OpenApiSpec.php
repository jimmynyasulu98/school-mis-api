<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="School MIS API",
 *     version="1.0.0",
 *     description="High school management information system API built with Laravel."
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Primary API server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token"
 * )
 * @OA\Tag(name="Auth", description="Authentication operations")
 * @OA\Tag(name="Students", description="Student management")
 * @OA\Tag(name="Staff", description="Staff management")
 * @OA\Tag(name="Academic Years", description="Academic year management")
 * @OA\Tag(name="Terms", description="Academic term management")
 * @OA\Tag(name="Classes", description="Classroom management")
 * @OA\Tag(name="Subjects", description="Subject catalog management")
 * @OA\Tag(name="Class Subjects", description="Subject offerings per class and assigned teachers")
 * @OA\Tag(name="Assessments", description="Assessment management")
 * @OA\Tag(name="Grades", description="Grade entry and reporting")
 * @OA\Tag(name="Payments", description="Fee payments and balances")
 * @OA\Tag(name="Attendance", description="Student attendance")
 * @OA\Tag(name="Roles", description="Role management")
 * @OA\Tag(name="Permissions", description="Permission management")
 * @OA\Tag(name="Reports", description="Reporting endpoints")
 */
class OpenApiSpec
{
}
