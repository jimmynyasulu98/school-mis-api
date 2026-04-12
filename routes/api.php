<?php

use App\Http\Controllers\API\AcademicYearController;
use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClassRoomController;
use App\Http\Controllers\API\GradeController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\ReportCardController;
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

        Route::get('students', [StudentController::class, 'index'])->middleware('permission:students.view');
        Route::post('students', [StudentController::class, 'store'])->middleware('permission:students.manage');
        Route::get('students/{student}', [StudentController::class, 'show'])->middleware('permission:students.view');
        Route::match(['put', 'patch'], 'students/{student}', [StudentController::class, 'update'])->middleware('permission:students.manage');
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->middleware('permission:students.manage');

        Route::get('staff', [StaffController::class, 'index'])->middleware('permission:staff.view');
        Route::post('staff', [StaffController::class, 'store'])->middleware('permission:staff.manage');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->middleware('permission:staff.view');
        Route::match(['put', 'patch'], 'staff/{staff}', [StaffController::class, 'update'])->middleware('permission:staff.manage');
        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->middleware('permission:staff.manage');

        Route::apiResource('academic-years', AcademicYearController::class)->middleware('permission:reports.view');
        Route::apiResource('terms', TermController::class)->except(['destroy'])->middleware('permission:reports.view');
        Route::apiResource('classes', ClassRoomController::class)->middleware('permission:reports.view');
        Route::apiResource('subjects', SubjectController::class)->middleware('permission:reports.view');
        Route::apiResource('assessments', AssessmentController::class)->except(['destroy'])->middleware('permission:grades.manage');
        Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'show'])->middleware('permission:staff.manage');
        Route::apiResource('permissions', PermissionController::class)->only(['index', 'store'])->middleware('permission:staff.manage');

        Route::get('grades', [GradeController::class, 'index'])->middleware('permission:grades.manage');
        Route::post('grades', [GradeController::class, 'store'])->middleware('permission:grades.manage');
        Route::get('attendance', [AttendanceController::class, 'index'])->middleware('permission:students.view');
        Route::post('attendance', [AttendanceController::class, 'store'])->middleware('permission:students.manage');
        Route::get('payments', [PaymentController::class, 'index'])->middleware('permission:finance.manage');
        Route::post('payments', [PaymentController::class, 'store'])->middleware('permission:finance.manage');

        Route::get('students/{student}/fees', [PaymentController::class, 'studentFees'])->middleware('permission:finance.manage');
        Route::get('students/{student}/report-card', [ReportCardController::class, 'show'])->middleware('permission:reports.view');
        Route::get('finance/reports/collections', [PaymentController::class, 'collections'])->middleware('permission:finance.manage');
    });
});
