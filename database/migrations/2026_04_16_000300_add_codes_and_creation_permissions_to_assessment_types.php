<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_types', function (Blueprint $table) {
            $table->string('code', 100)->nullable()->after('name');
            $table->string('creation_permission')->nullable()->after('weight');
        });

        DB::table('assessment_types')
            ->whereRaw('LOWER(name) = ?', ['continuous test'])
            ->update(['code' => 'continuous_test']);

        DB::table('assessment_types')
            ->whereRaw('LOWER(name) = ?', ['mid term exam'])
            ->update(['code' => 'mid_term_exam']);

        DB::table('assessment_types')
            ->whereRaw('LOWER(name) = ?', ['end of term exam'])
            ->update([
                'code' => 'end_of_term_exam',
                'creation_permission' => 'assessments.create.end-of-term',
            ]);

        DB::table('assessment_types')
            ->whereNull('code')
            ->orderBy('id')
            ->get()
            ->each(function (object $assessmentType): void {
                DB::table('assessment_types')
                    ->where('id', $assessmentType->id)
                    ->update([
                        'code' => str($assessmentType->name)->lower()->snake()->value(),
                    ]);
            });

        Schema::table('assessment_types', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_types', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'creation_permission']);
        });
    }
};
