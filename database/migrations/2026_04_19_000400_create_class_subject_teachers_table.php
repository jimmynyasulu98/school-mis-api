<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subject_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_subject_id')->constrained()->cascadeOnDelete();
            $table->uuid('teacher_id');
            $table->boolean('is_core')->default(false);
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('staff')->cascadeOnDelete();
            $table->unique(['class_subject_id', 'teacher_id']);
        });

        $legacyAssignments = DB::table('class_subjects')
            ->whereNotNull('teacher_id')
            ->select(['id', 'teacher_id', 'created_at', 'updated_at'])
            ->get();

        foreach ($legacyAssignments as $assignment) {
            DB::table('class_subject_teachers')->insert([
                'class_subject_id' => $assignment->id,
                'teacher_id' => $assignment->teacher_id,
                'is_core' => true,
                'created_at' => $assignment->created_at ?? now(),
                'updated_at' => $assignment->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subject_teachers');
    }
};
