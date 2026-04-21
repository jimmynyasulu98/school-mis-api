<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_subject_teachers', function (Blueprint $table) {
            $table->date('starts_on')->nullable()->after('is_core');
            $table->date('ends_on')->nullable()->after('starts_on');
            $table->dropUnique('class_subject_teachers_class_subject_id_teacher_id_unique');
        });

        DB::table('class_subject_teachers')
            ->select(['id', 'created_at'])
            ->orderBy('id')
            ->get()
            ->each(function ($assignment) {
                DB::table('class_subject_teachers')
                    ->where('id', $assignment->id)
                    ->update([
                        'starts_on' => optional($assignment->created_at)->format('Y-m-d') ?? now()->toDateString(),
                    ]);
            });

        Schema::table('class_subject_teachers', function (Blueprint $table) {
            $table->date('starts_on')->nullable(false)->change();
            $table->index(['class_subject_id', 'starts_on']);
            $table->index(['class_subject_id', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::table('class_subject_teachers', function (Blueprint $table) {
            $table->dropIndex(['class_subject_id', 'starts_on']);
            $table->dropIndex(['class_subject_id', 'ends_on']);
            $table->unique(['class_subject_id', 'teacher_id']);
            $table->dropColumn(['starts_on', 'ends_on']);
        });
    }
};
