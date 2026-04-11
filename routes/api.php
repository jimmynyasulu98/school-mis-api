<?php

use App\Http\Controllers\API\AcademicYearController;
use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClassRoomController;
use App\Http\Controllers\API\FinanceController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\TermController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::apiResource('students', StudentController::class);
        Route::apiResource('staff', StaffController::class);
        Route::apiResource('academic-years', AcademicYearController::class);
        Route::apiResource('terms', TermController::class)->except(['destroy']);
        Route::apiResource('classes', ClassRoomController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('assessments', AssessmentController::class)->except(['destroy']);
        Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'show']);
        Route::apiResource('permissions', PermissionController::class)->only(['index', 'store']);

        Route::get('students/{student}/report-card', [FinanceController::class, 'reportCard']);
        Route::get('students/{student}/fees', [FinanceController::class, 'studentFees']);
        Route::get('finance/reports/collections', [FinanceController::class, 'collections']);
    });
});
