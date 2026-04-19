<?php

use App\Http\Controllers\API\AcademicYearController;
use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClassRoomController;
use App\Http\Controllers\API\ClassSubjectController;
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

        // Students routes
        Route::get('students', [StudentController::class, 'index'])->middleware('permission:students.view');
        Route::post('students', [StudentController::class, 'store'])->middleware('permission:students.create');
        Route::get('students/{student}', [StudentController::class, 'show'])->middleware('permission:students.view');
        Route::match(['put', 'patch'], 'students/{student}', [StudentController::class, 'update'])->middleware('permission:students.edit');
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->middleware('permission:students.delete');

        // Staff routes
        Route::get('staff', [StaffController::class, 'index'])->middleware('permission:staff.view');
        Route::post('staff', [StaffController::class, 'store'])->middleware('permission:staff.create');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->middleware('permission:staff.view');
        Route::match(['put', 'patch'], 'staff/{staff}', [StaffController::class, 'update'])->middleware('permission:staff.edit');
        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->middleware('permission:staff.delete');

        // Academic Years
        Route::get('academic-years', [AcademicYearController::class, 'index'])->middleware('permission:academic-years.view');
        Route::post('academic-years', [AcademicYearController::class, 'store'])->middleware('permission:academic-years.create');
        Route::get('academic-years/{academicYear}', [AcademicYearController::class, 'show'])->middleware('permission:academic-years.view');
        Route::match(['put', 'patch'], 'academic-years/{academicYear}', [AcademicYearController::class, 'update'])->middleware('permission:academic-years.edit');
        Route::delete('academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->middleware('permission:academic-years.delete');

        // Terms
        Route::get('terms', [TermController::class, 'index'])->middleware('permission:terms.view');
        Route::post('terms', [TermController::class, 'store'])->middleware('permission:terms.create');
        Route::get('terms/{term}', [TermController::class, 'show'])->middleware('permission:terms.view');
        Route::match(['put', 'patch'], 'terms/{term}', [TermController::class, 'update'])->middleware('permission:terms.edit');

        // Classes
        Route::get('classes', [ClassRoomController::class, 'index'])->middleware('permission:classes.view');
        Route::post('classes', [ClassRoomController::class, 'store'])->middleware('permission:classes.create');
        Route::get('classes/{classroom}', [ClassRoomController::class, 'show'])->middleware('permission:classes.view');
        Route::match(['put', 'patch'], 'classes/{classroom}', [ClassRoomController::class, 'update'])->middleware('permission:classes.edit');
        Route::delete('classes/{classroom}', [ClassRoomController::class, 'destroy'])->middleware('permission:classes.delete');

        // Subjects
        Route::get('subjects', [SubjectController::class, 'index'])->middleware('permission:subjects.view');
        Route::post('subjects', [SubjectController::class, 'store'])->middleware('permission:subjects.create');
        Route::get('subjects/{subject}', [SubjectController::class, 'show'])->middleware('permission:subjects.view');
        Route::match(['put', 'patch'], 'subjects/{subject}', [SubjectController::class, 'update'])->middleware('permission:subjects.edit');
        Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->middleware('permission:subjects.delete');

        // Class subjects / teaching assignments
        Route::get('class-subjects', [ClassSubjectController::class, 'index'])->middleware('permission:class-subjects.view');
        Route::post('class-subjects', [ClassSubjectController::class, 'store'])->middleware('permission:class-subjects.create');
        Route::get('class-subjects/{classSubject}', [ClassSubjectController::class, 'show'])->middleware('permission:class-subjects.view');
        Route::delete('class-subjects/{classSubject}', [ClassSubjectController::class, 'destroy'])->middleware('permission:class-subjects.delete');
        Route::post('class-subjects/{classSubject}/teachers', [ClassSubjectController::class, 'assignTeacher'])->middleware('permission:class-subjects.edit');
        Route::patch('class-subjects/{classSubject}/teachers/{teacher}/core', [ClassSubjectController::class, 'switchCoreTeacher'])->middleware('permission:class-subjects.edit');
        Route::delete('class-subjects/{classSubject}/teachers/{teacher}', [ClassSubjectController::class, 'unassignTeacher'])->middleware('permission:class-subjects.edit');

        // Assessments
        Route::get('assessments', [AssessmentController::class, 'index'])->middleware('permission:assessments.view');
        Route::post('assessments', [AssessmentController::class, 'store'])->middleware('permission:assessments.create');
        Route::get('assessments/{assessment}', [AssessmentController::class, 'show'])->middleware('permission:assessments.view');
        Route::match(['put', 'patch'], 'assessments/{assessment}', [AssessmentController::class, 'update'])->middleware('permission:assessments.edit');

        // Roles and Permissions
        Route::get('roles', [RoleController::class, 'index'])->middleware('permission:roles.manage');
        Route::post('roles', [RoleController::class, 'store'])->middleware('permission:roles.manage');
        Route::get('roles/{role}', [RoleController::class, 'show'])->middleware('permission:roles.manage');
        Route::get('permissions', [PermissionController::class, 'index'])->middleware('permission:roles.manage');
        Route::post('permissions', [PermissionController::class, 'store'])->middleware('permission:roles.manage');

        // Grades
        Route::get('grades', [GradeController::class, 'index'])->middleware('permission:grades.view');
        Route::post('grades', [GradeController::class, 'store'])->middleware('permission:grades.create');
        Route::match(['put', 'patch'], 'grades/{grade}', [GradeController::class, 'update'])->middleware('permission:grades.edit');
        Route::delete('grades/{grade}', [GradeController::class, 'destroy'])->middleware('permission:grades.delete');

        // Attendance
        Route::get('attendance', [AttendanceController::class, 'index'])->middleware('permission:attendance.view');
        Route::post('attendance', [AttendanceController::class, 'store'])->middleware('permission:attendance.create');
        Route::match(['put', 'patch'], 'attendance/{attendance}', [AttendanceController::class, 'update'])->middleware('permission:attendance.edit');
        Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->middleware('permission:attendance.delete');

        // Payments
        Route::get('payments', [PaymentController::class, 'index'])->middleware('permission:payments.view');
        Route::post('payments', [PaymentController::class, 'store'])->middleware('permission:payments.create');
        Route::match(['put', 'patch'], 'payments/{payment}', [PaymentController::class, 'update'])->middleware('permission:payments.edit');
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->middleware('permission:payments.delete');

        // Special endpoints
        Route::get('students/{student}/fees', [PaymentController::class, 'studentFees'])->middleware('permission:payments.view');
        Route::get('students/{student}/report-card', [ReportCardController::class, 'show'])->middleware('permission:reports.academic');
        Route::get('finance/reports/collections', [PaymentController::class, 'collections'])->middleware('permission:reports.finance');
    });
});
