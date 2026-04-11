<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('relationship', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('employee_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 20)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('job_title', 100)->nullable();
            $table->date('hire_date')->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('staff_id')->references('id')->on('staff')->nullOnDelete();
            $table->foreign('guardian_id')->references('id')->on('guardians')->nullOnDelete();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('staff_role', function (Blueprint $table) {
            $table->uuid('staff_id');
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['staff_id', 'role_id']);
            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name', 30);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->unique(['academic_year_id', 'name']);
        });

        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('stream', 20)->nullable();
            $table->string('section', 30)->nullable();
            $table->uuid('class_teacher_id')->nullable();
            $table->timestamps();
            $table->foreign('class_teacher_id')->references('id')->on('staff')->nullOnDelete();
            $table->unique(['name', 'stream']);
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->boolean('is_core')->default(false);
            $table->timestamps();
        });

        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->uuid('teacher_id')->nullable();
            $table->timestamps();
            $table->foreign('teacher_id')->references('id')->on('staff')->nullOnDelete();
            $table->unique(['class_room_id', 'subject_id']);
        });

        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('admission_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('enrollment_date');
            $table->string('status', 30)->default('ACTIVE');
            $table->foreignId('current_class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'current_class_room_id']);
        });

        Schema::create('student_guardian', function (Blueprint $table) {
            $table->uuid('student_id');
            $table->uuid('guardian_id');
            $table->boolean('is_primary')->default(false);
            $table->string('notes')->nullable();
            $table->primary(['student_id', 'guardian_id']);
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('guardian_id')->references('id')->on('guardians')->cascadeOnDelete();
        });

        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->date('enrollment_date');
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->unique(['student_id', 'class_room_id', 'term_id']);
        });

        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('weight', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('class_subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_type_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('max_score', 8, 2);
            $table->date('assessment_date');
            $table->timestamps();
        });

        Schema::create('student_grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('assessment_id');
            $table->decimal('score', 8, 2);
            $table->string('grade_letter', 5)->nullable();
            $table->text('remarks')->nullable();
            $table->uuid('recorded_by')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete();
            $table->foreign('recorded_by')->references('id')->on('staff')->nullOnDelete();
            $table->unique(['student_id', 'assessment_id']);
        });

        Schema::create('student_grade_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->decimal('final_average', 8, 2)->nullable();
            $table->unsignedInteger('position')->nullable();
            $table->string('promotion_status', 30)->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
            $table->unique(['class_room_id', 'academic_year_id', 'term_id']);
        });

        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('student_fee_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->unique(['student_id', 'fee_structure_id']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_fee_account_id');
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50)->nullable();
            $table->string('receipt_number')->unique();
            $table->uuid('recorded_by')->nullable();
            $table->timestamps();
            $table->foreign('student_fee_account_id')->references('id')->on('student_fee_accounts')->cascadeOnDelete();
            $table->foreign('recorded_by')->references('id')->on('staff')->nullOnDelete();
        });

        Schema::create('staff_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('staff_id');
            $table->uuid('evaluator_id')->nullable();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 8, 2)->nullable();
            $table->text('comments')->nullable();
            $table->date('assessment_date');
            $table->timestamps();
            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->foreign('evaluator_id')->references('id')->on('staff')->nullOnDelete();
        });

        Schema::create('staff_assessment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('staff_id');
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->decimal('overall_rating', 8, 2)->nullable();
            $table->boolean('promotion_recommendation')->default(false);
            $table->timestamps();
            $table->foreign('staff_id')->references('id')->on('staff')->cascadeOnDelete();
        });

        Schema::create('attendance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status', 20);
            $table->uuid('recorded_by')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('recorded_by')->references('id')->on('staff')->nullOnDelete();
            $table->unique(['student_id', 'class_room_id', 'attendance_date']);
        });

        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_subject_id')->constrained()->cascadeOnDelete();
            $table->string('day_of_week', 12);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('message');
            $table->string('audience', 50);
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('staff')->nullOnDelete();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('action');
            $table->string('table_name');
            $table->string('record_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('staff_assessment_histories');
        Schema::dropIfExists('staff_assessments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('student_fee_accounts');
        Schema::dropIfExists('fee_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('student_grade_histories');
        Schema::dropIfExists('student_grades');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('assessment_types');
        Schema::dropIfExists('class_enrollments');
        Schema::dropIfExists('student_guardian');
        Schema::dropIfExists('students');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('class_rooms');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('staff_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropForeign(['guardian_id']);
        });
        Schema::dropIfExists('staff');
        Schema::dropIfExists('guardians');
    }
};
