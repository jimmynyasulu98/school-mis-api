<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'guard_name')) {
                $table->string('guard_name')->default('api')->after('name');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'guard_name')) {
                $table->string('guard_name')->default('api')->after('name');
            }
        });

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->primary(['permission_id', 'role_id']);
            });
        }

        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->uuid('model_id');
                $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
                $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->primary(['role_id', 'model_id', 'model_type']);
            });
        }

        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->uuid('model_id');
                $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
                $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
                $table->primary(['permission_id', 'model_id', 'model_type']);
            });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->uuidMorphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');

        Schema::table('permissions', function (Blueprint $table) {
            if (Schema::hasColumn('permissions', 'guard_name')) {
                $table->dropColumn('guard_name');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'guard_name')) {
                $table->dropColumn('guard_name');
            }
        });
    }
};


