<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add is_active to students table
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });

        // Add fields to class_enrollments table
        Schema::table('class_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('class_enrollments', 'enrollment_type')) {
                $table->string('enrollment_type', 50)->default('MANUAL')->after('status');
            }
            if (!Schema::hasColumn('class_enrollments', 'promoted_from_class_id')) {
                $table->unsignedBigInteger('promoted_from_class_id')->nullable()->after('enrollment_type');
            }
            if (!Schema::hasColumn('class_enrollments', 'enrolled_by')) {
                $table->uuid('enrolled_by')->nullable()->after('promoted_from_class_id');
            }
            if (!Schema::hasColumn('class_enrollments', 'promotion_reason')) {
                $table->text('promotion_reason')->nullable()->after('enrolled_by');
            }

            // Add indexes
            $table->index('enrollment_type');
            $table->foreign('promoted_from_class_id')->references('id')->on('class_rooms')->nullOnDelete();
            $table->foreign('enrolled_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('class_enrollments', 'promoted_from_class_id')) {
                $table->dropForeign('class_enrollments_promoted_from_class_id_foreign');
                $table->dropColumn('promoted_from_class_id');
            }
            if (Schema::hasColumn('class_enrollments', 'enrolled_by')) {
                $table->dropForeign('class_enrollments_enrolled_by_foreign');
                $table->dropColumn('enrolled_by');
            }
            if (Schema::hasColumn('class_enrollments', 'promotion_reason')) {
                $table->dropColumn('promotion_reason');
            }
            if (Schema::hasColumn('class_enrollments', 'enrollment_type')) {
                $table->dropIndex(['enrollment_type']);
                $table->dropColumn('enrollment_type');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
